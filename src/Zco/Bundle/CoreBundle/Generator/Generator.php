<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\CoreBundle\Generator;

use Zco\Bundle\CoreBundle\Paginator\Paginator;

/**
 * Classe used to generate an admin site. All the controller and template code
 * is generated by this class. The configuration must be done in editing a
 * generator.yml file, never in overwriting the existing methods (except for
 * batch actions).
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */

use Zco\Bundle\CoreBundle\Templating\Helper\HumanizeHelper;
use Zco\Bundle\CoreBundle\Templating\Helper\UiHelper;
use Zco\Bundle\CoreBundle\Templating\Helper\ResourcesHelper;
use Zco\Component\Templating\TemplatingEvents;
use Zco\Component\Templating\Event\FilterResourcesEvent;
use Zco\Component\Templating\Event\FilterVariablesEvent;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Generator extends Controller
{
	protected
		$modelName = null,
		$table     = null,
		$fields    = array(),
		$config    = array();

	/**
	 * Constructor. Load the configuration from the YAML file.
	 */
	public function __construct()
	{
		$this->table = \Doctrine_Core::getTable($this->modelName);
		foreach ($this->table->getColumnNames() as $name)
		{
			$this->fields[$name] = new GeneratorColumn($name, $this->table);
		}

		\Config::load('generator', true);
		$config = \Config::get('generator');
		if(!isset($config[$this->modelName]))
			throw new \RuntimeException(sprintf('generator.yml must contain a "%s" section.', $this->modelName));
		$this->config = $config[$this->modelName];
	}

	protected function getListQuery() {}

	###################
	##  LIST METHOD  ##
	###################
	/**
	 * Display a list of elements, with actions on each object, batch actions and
	 * global actions. Allow the user to sort the list, to filter, to search, etc.
	 * @return Response
	 */
	public function executeList()
	{
		//--- Configuration merging ---
		$config = array(
			'config'  => array(),
			'actions' => array(),
			'fields'  => array(),
			'list'    => array(),
		);

		$config['fields'] = \Util::arrayDeepMerge(
			isset($this->config['fields']) ? $this->config['fields'] : array(),
			isset($this->config['list']['fields']) ? $this->config['list']['fields'] : array()
		);
		$config['config'] = \Util::arrayDeepMerge(
			array(
				'singular' => \Util_Inflector::humanize($this->modelName),
				'plural'   => \Util_Inflector::humanize($this->modelName).'s',
			),
			isset($this->config['config']) ? $this->config['config'] : array()
		);
		$config['list'] = \Util::arrayDeepMerge(
			array(
				'title'          => sprintf('Liste des %s', lcfirst($config['config']['plural'])),
				'description'    => '',
				'display'        => null,
				'max_per_page'   => 20,
				'ordering'       => null,
				'table_method'   => null,
				'layout'         => 'tabular',
				'search_field'   => array(),
				'filter'         => array(),
				'object_actions' => array('_edit' => null, '_delete' => null),
				'batch_actions'  => array('_delete' => null),
				'actions'        => array('_new' => null),
				'fields'         => array(),
			),
			isset($this->config['list']) ? $this->config['list'] : array()
		);
		unset($config['list']['fields']);
		if (empty($config['list']['display']))
		{
			$i = 0;
			foreach($this->fields as $name => $field)
			{
				$config['list']['display'][] =  (!$i ? '='.$name : $name) ;
				$i++;
			}
		}

		$config['actions'] = \Util::arrayDeepMerge(
			array(
				'_delete' => array('label' => 'Supprimer', 'route' => 'supprimer-%id%.html', 'icon' => '/bundles/zcocore/img/generator/delete.png', 'credentials' => array()),
				'_edit'   => array('label' => 'Modifier', 'route' => 'modifier-%id%.html', 'icon' => '/bundles/zcocore/img/generator/edit.png', 'credentials' => array()),
				'_new'    => array('label' => 'Ajouter un nouvel élément', 'route' => 'ajouter.html', 'credentials' => array()),
				'_list'   => array('label' => 'Liste des éléments', 'route' => 'index-p%d.html', 'credentials' => array()),
			),
			isset($this->config['actions']) ? $this->config['actions'] : array()
		);

		//--- Batch actions ---
		if (isset($_POST['batch_action']))
		{
			if ($_POST['batch_action'][0] == '_')
				$_POST['batch_action'] = substr($_POST['batch_action'], 1);

			$method = 'batch'.ucfirst($_POST['batch_action']);
			$ret = $this->$method(array_keys($_POST['objects']));
			if ($ret instanceof Response)
				return $ret;
			else
				return redirect('Les actions multiples ont bien été effectuées.');
		}

		//--- Model part: construction of the Doctrine_Query ---
		$query = $this->getListQuery();
		if(!$query)
			$query = \Doctrine_Query::create()->from($this->modelName);

		//Ordering.
		if (!empty($_GET['_orderby']))
		{
			$query->orderBy($this->getOrderBy($_GET['_orderby']));
		}
		elseif (isset($config['list']['ordering']))
		{
			if (is_array($config['list']['ordering']))
			{
				$i = 0;
				foreach ($config['list']['ordering'] as $orderby)
				{
					if ($i == 0)
						$query->orderBy($this->getOrderBy($orderby));
					else
						$query->addOrderBy($this->getOrderBy($orderby));
					$i++;
				}
			}
			else
			{
				$query->orderBy($this->getOrderBy($config['list']['ordering']));
			}
		}

		//Deal with filters passed in the url.
		$filters_url = $_GET;
		unset(
			$filters_url['page'], $filters_url['act'], $filters_url['p'],
			$filters_url['titre'], $filters_url['_orderby'], $filters_url['index'],
			$filters_url['debug'], $filters_url['del_cache'], $filters_url['all'],
			$filters_url['id'], $filters_url['id2']
		);

		//Gestion de la hiérarchie par la date si elle est définie.
		//TODO.

		foreach ($filters_url as $key => $value)
		{
			if (is_array($config['list']['search_field']) && in_array($key, $config['list']['search_field']) ||
				!is_array($config['list']['search_field']) && $key == $config['list']['search_field'])
				$query->addWhere($query->getRootAlias().'.'.$key.' LIKE ?', '%'.$value.'%');
			else
				$query->addWhere($query->getRootAlias().'.'.$key.' = ?', $value);
		}

		//Build the query string.
		if (isset($_GET['all']))
			$filters_url['all'] = 1;
		$filters_url = '?'.http_build_query($filters_url);

		//Table method.
		if (!empty($config['list']['table_method']))
		{
			$query = $this->table->$config['list']['table_method']($query);
		}

		//Pagination.
		$page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
		$paginator = new Paginator($query, $config['list']['max_per_page']);
		$paginatorPage = $paginator->createView($page);
		$objects = $paginatorPage->getObjects();
		$count_objects_total = $paginator->count();

		//--- Display part ---
		if(!in_array($config['list']['layout'], array('tabular', 'stacked')))
			throw new \RuntimeException(sprintf('Layout "%s" isn\'t defined. Valid values are "stacked" or "tabular".'));

		$colspan = count($config['list']['display']) +
			(!empty($config['list']['object_actions']) ? 1 : 0) +
			(!empty($config['list']['batch_actions'])  ? 1 : 0 );

		//Search field stuff.
		if (!empty($config['list']['search_field']))
		{
			$search = array(
				'name' => $config['list']['search_field'],
				'value' => isset($_GET[$config['list']['search_field']]) ? $_GET[$config['list']['search_field']] : '',
				'default' => sprintf('Rechercher par %s...',
					isset($config['fields'][$config['list']['search_field']]['label']) ?
						lcfirst($config['fields'][$config['list']['search_field']]['label']) :
						lcfirst(\Util_Inflector::humanize($config['list']['search_field']))
				),
			);
		}

		//Filters stuff.
		if(!empty($config['list']['filter']))
		{
			$filters = array();
			foreach($config['list']['filter'] as $name)
			{
				if(!isset($this->fields[$name]))
					throw new \RuntimeException(sprintf('No field named %s.', $name));
				$column = $this->fields[$name];

				//Boolean.
				if($column->getType() == 'boolean')
				{
					$choices = array(
						1 => 'Oui',
						0 => 'Non',
					);
				}
				//Enum.
				elseif($column->getType() == 'enum')
				{
					$choices = $column['values'];
				}
				//String.
				elseif($column->getType() == 'string' && $column->getLength() <= 255)
				{
					$choices = \Doctrine_Query::create()
						->select('DISTINCT '.$column->getName())
						->from($this->modelName)
						->orderBy($column->getName())
						->execute();
					foreach($choices as $key => $value)
					{
						$choices[$value[$column->getName()]] = $value[$column->getName()];
						unset($choices[$key]);
					}
				}
				//Else non-supported field.
				else
				{
					throw new \RuntimeException(sprintf(
						'Field %s of type %s is not supported by filters. It must be a boolean, char or enum field.',
						$column->getName(),
						$column->getType()
					));
				}

				$filters[] = array(
					'label'       => !empty($config['fields'][$name]['label']) ?
						$config['fields'][$name]['label'] : Util_Inflector::humanize($name),
					'name' => $name,
					'choices'     => $choices,
					'value'       => isset($_GET[$name]) ? $name : '',
					'url'         => isset($_GET[$name]) ? sprintf('%s=%s', $name, $_GET[$name]) : '',
				);
			}
		}
		
		$paginatorPage->setUri($config['actions']['_list']['route'].$filters_url);

		//--- Table construction ---
		$table = array('thead' => array(), 'tbody' => array());
		foreach($config['list']['display'] as $name)
		{
			if ($name[0] == '=')
			{
				$link = true;
				$name = substr($name, 1);
			}
			else
				$link = false;

			if (strpos($name, '::'))
			{
				$partial = $name;
				$name = substr($name, strpos($name, '::') + 3, strpos($name, '.') - (strpos($name, '::') + 3));
			}
			else
				$partial = false;

			$attrs = isset($config['fields'][$name]['attributes']) ?
				implode(' ',
					array_map(
					    function($k, $v) {
                            return $k.'="'.$v.'"';
                        },
						array_keys($config['fields'][$name]['attributes']),
						array_values($config['fields'][$name]['attributes'])
					)
				) : '';
			$credentials = isset($config['fields'][$name]['credentials']) ?
				verifier_array($config['fields'][$name]['credentials']) : true;
			$label = isset($config['fields'][$name]['label']) ?
				$config['fields'][$name]['label'] : \Util_Inflector::humanize($name);

			//Thead element, with the label.
			$table['thead'][$name] = array(
				'label'       => $label,
				'credentials' => $credentials,
				'attrs'       => $attrs,
			);

			//Tbody element with the field rendered.
			if (!$partial)
			{
				$table['tbody'][$name] = array(
					'content'       => $this->renderField($name),
					'credentials'   => $credentials,
					'attrs'         => $attrs,
					'is_partial'    => $partial,
					'is_link'       => $link,
				);
			}
			else
			{
				$table['tbody'][$name] = array(
					'content'       => sprintf('echo $view->render(\'%s\', array(\'row\' => $row));', $partial),
					'credentials'   => $credentials,
					'attrs'         => $attrs,
					'is_partial'    => $partial,
					'is_link'       => $link,
				);
			}

		}

		//Inclusion de la vue et décoration avec le layout.
		fil_ariane($config['list']['title']);
		\Page::$titre = $config['list']['title'];
		$this->get('zco_vitesse.resource_manager')->requireResources(array(
		    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
		    '@ZcoCoreBundle/Resources/public/css/zcode.css',
		    '@ZcoCoreBundle/Resources/public/css/generator.css',
		    
		    '@ZcoCoreBundle/Resources/public/js/generator.js',
		    '@ZcoCoreBundle/Resources/public/js/messages.js',
		));
		$paginate = true;
		
		return render_to_response('ZcoCoreBundle:Generator:list.html.php', get_defined_vars());
	}

	##################
	##  NEW METHOD  ##
	##################
	/**
	 * Display a form in order to create a new object.
	 * @return Response
	 */
	public function executeNew()
	{
		$config = array();
		$config['actions'] = \Util::arrayDeepMerge(
			array(
				'_delete' => array('label' => 'Supprimer', 'route' => 'supprimer-%id%.html', 'icon' => '/bundles/zcocore/img/generator/delete.png', 'credentials' => array()),
				'_edit'   => array('label' => 'Modifier', 'route' => 'modifier-%id%.html', 'icon' => '/bundles/zcocore/img/generator/edit.png', 'credentials' => array()),
				'_new'    => array('label' => 'Ajouter un nouvel élément', 'route' => 'ajouter.html', 'credentials' => array()),
				'_list'   => array('label' => 'Liste des éléments', 'route' => 'index-p%d.html', 'credentials' => array()),
			),
			isset($this->config['actions']) ? $this->config['actions'] : array()
		);
		$config['fields'] = \Util::arrayDeepMerge(
			isset($this->config['fields']) ? $this->config['fields'] : array(),
			isset($this->config['new']['fields']) ? $this->config['new']['fields'] : array()
		);
		$config['new'] = \Util::arrayDeepMerge(
			array(
				'title' => 'Ajouter un nouvel élément',
				'description' => '',
			),
			isset($this->config['new']) ? $this->config['new'] : array()
		);
		$config['form'] = \Util::arrayDeepMerge(
			isset($this->config['form']) ? $this->config['form'] : array(),
			isset($this->config['new']['form']) ? $this->config['new']['form'] : array()
		);
		$action = 'new';
		$form = $this->getForm($config);

		if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
		{
			$form->bind($_POST);
			if($form->isValid())
			{
				$className = $this->modelName;
				$object = new $className;
				foreach ($form->getCleanedData() as $key => $value)
				{
					if (isset($this->fields[$key]))
					{
						$column = $this->fields[$key];
						$object[$column->getFieldName()]= $value;
					}
				}
				$object->save();

				if(isset($_POST['save']))
					return redirect('L\'élément a bien été ajouté.', sprintf($config['actions']['_list']['route'], 1));
				if(isset($_POST['save_edit']))
					return redirect('L\'élément a bien été ajouté.', str_replace('%id%', $object['id'], $config['actions']['_edit']['route']));
				if(isset($_POST['save_new']))
					return redirect('L\'élément a bien été ajouté.', $config['actions']['_new']['route']);
			}
		}

		if (!empty($config['form']['prepopulated_fields']))
			$form->setDefaults($config['form']['prepopulated_fields']);

		//Inclusion de la vue et décoration avec le layout.
		\Page::$titre = $config['new']['title'];
		fil_ariane($config['new']['title']);
		$this->get('zco_vitesse.resource_manager')->requireResources(array(
		    '@ZcoCoreBundle/Resources/public/css/zcode.css',
		    '@ZcoCoreBundle/Resources/public/css/generator.css',
		    
		    '@ZcoCoreBundle/Resources/public/js/generator.js',
		    '@ZcoCoreBundle/Resources/public/js/form.js',
		));

		return render_to_response('ZcoCoreBundle:Generator:new.html.php', get_defined_vars());
	}

	###################
	##  EDIT METHOD  ##
	###################
	/**
	 * Display a form in order to edit an object.
	 * @param integer $pk		The primary key of the object to edit.
	 * @return Response
	 */
	public function executeEdit($pk)
	{
		if(!is_numeric($pk))
			throw new \InvalidArgumentException(sprintf('Generator::generateEdit() must receive an integer (got %s).', gettype($pk)));

		$config['actions'] = \Util::arrayDeepMerge(
			array(
				'_delete' => array('label' => 'Supprimer', 'route' => 'supprimer-%id%.html', 'icon' => '/bundles/zcocore/img/generator/delete.png', 'credentials' => array()),
				'_edit'   => array('label' => 'Modifier', 'route' => 'modifier-%id%.html', 'credentials' => array()),
				'_new'    => array('label' => 'Ajouter un nouvel élément', 'route' => 'ajouter.html', 'credentials' => array()),
				'_list'   => array('label' => 'Liste des éléments', 'route' => 'index-p%d.html', 'credentials' => array()),
			),
			isset($this->config['actions']) ? $this->config['actions'] : array()
		);

		$object = \Doctrine_Core::getTable($this->modelName)->find($pk);
		if($object === false)
			return redirect('L\'élément spécifié n\'existe pas.',
				sprintf($config['actions']['_list']['route'], 1), MSG_ERROR);

		$config['fields'] = \Util::arrayDeepMerge(
			isset($this->config['fields']) ? $this->config['fields'] : array(),
			isset($this->config['edit']['fields']) ? $this->config['edit']['fields'] : array()
		);
		$config['new'] = \Util::arrayDeepMerge(
			array(
				'title' => 'Modifier un élément',
				'description' => '',
			),
			isset($this->config['edit']) ? $this->config['edit'] : array()
		);
		$config['form'] = \Util::arrayDeepMerge(
			isset($this->config['form']) ? $this->config['form'] : array(),
			isset($this->config['edit']['form']) ? $this->config['edit']['form'] : array()
		);

		$action = 'edit';
		$form = $this->getForm($config, $object);

		//Si jamais des données ont été envoyées.
		if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
		{
			$form->bind($_POST);
			if($form->isValid())
			{
				//Si on sauvegarde sous, on créé un nouvel objet.
				if(isset($_POST['save_as']))
				{
					$object = clone $object;
					$object['id'] = null;
				}

				foreach ($form->getCleanedData() as $key => $value)
				{
					if (isset($this->fields[$key]))
					{
						$column = $this->fields[$key];
						$object[$column->getFieldName()]= $value;
					}
				}
				$object->save();

				//Redirection en fonction du bouton qui a été pressé.
				if(isset($_POST['save']) || isset($_POST['save_as']))
					return redirect('L\'élément a bien été modifié.', sprintf($config['actions']['_list']['route'], 1));
				if(isset($_POST['save_edit']))
					return redirect('L\'élément a bien été modifié.', str_replace('%id%', $object['id'], $config['actions']['_edit']['route']));
				if(isset($_POST['save_new']))
					return redirect('L\'élément a bien été modifié.', $config['actions']['_list']['new']);
			}
		}

		//Inclusion de la vue et décoration avec le layout.
		$config['new']['title'] = $this->replaceReferences($config['new']['title'], $object);
		$config['new']['description'] = $this->replaceReferences($config['new']['description'], $object);
		\Page::$titre = $config['new']['title'];
		fil_ariane($config['new']['title']);
		$this->get('zco_vitesse.resource_manager')->requireResources(array(
		    '@ZcoCoreBundle/Resources/public/css/zcode.css',
		    '@ZcoCoreBundle/Resources/public/css/generator.css',
		    
		    '@ZcoCoreBundle/Resources/public/js/generator.js',
		    '@ZcoCoreBundle/Resources/public/js/form.js',
		));

		return render_to_response('ZcoCoreBundle:Generator:new.html.php', get_defined_vars());
	}

	######################
	##  DELETE METHODS  ##
	######################
	/**
	 * Display a page which asks confirmation for deleting an object.
	 * @param integer $pk		The primary key of the object to delete.
	 * @return Response
	 */
	public function executeDelete($pk)
	{
		if(!is_numeric($pk))
			throw new \InvalidArgumentException(sprintf('Generator::generateDelete() must receive an integer (got %s).', gettype($pk)));

		$action = 'delete';
		$config['actions'] = \Util::arrayDeepMerge(
			array(
				'_delete' => array('label' => 'Supprimer', 'route' => 'supprimer-%id%.html', 'icon' => '/bundles/zcocore/img/generator/delete.png', 'credentials' => array()),
				'_edit'   => array('label' => 'Modifier', 'route' => 'modifier-%id%.html', 'credentials' => array()),
				'_new'    => array('label' => 'Ajouter un nouvel élément', 'route' => 'ajouter.html', 'credentials' => array()),
				'_list'   => array('label' => 'Liste des éléments', 'route' => 'index-p%d.html', 'credentials' => array()),
			),
			isset($this->config['actions']) ? $this->config['actions'] : array()
		);
		$config['delete'] = \Util::arrayDeepMerge(
			array(
				'title' => 'Supprimer un élément',
				'description' => '',
				'message' => 'Voulez-vous vraiment supprimer cet élément ?',
			),
			isset($this->config['delete']) ? $this->config['delete'] : array()
		);

		$object = \Doctrine_Core::getTable($this->modelName)->find($pk);
		if($object === false)
			return redirect('L\'élément spécifié n\'existe pas.', sprintf($config['actions']['_list']['route'], 1), MSG_ERROR);

		//If cancel.
		if(isset($_POST['no']))
		{
			return new RedirectResponse(sprintf($config['actions']['_list']['route'], 1));
		}

		//If confirm.
		if(isset($_POST['yes']) && $_POST['token'] == $_SESSION['token'])
		{
			$object->delete();
			return redirect('L\'élement a bien été supprimé.', sprintf($config['actions']['_list']['route'], 1));
		}

		//Affectation des variables de présentation.
		$title = $this->replaceReferences($config['delete']['title'], $object);
		$description = $config['delete']['description'];
		$message = $this->replaceReferences($config['delete']['message'], $object);

		//Inclusion de la vue et décoration avec le layout.
		\Page::$titre = $title;
		fil_ariane($title);
		$this->get('zco_vitesse.resource_manager')->requireResources(array(
		    '@ZcoCoreBundle/Resources/public/css/generator.css',
		    '@ZcoCoreBundle/Resources/public/js/generator.js',
		    '@ZcoCoreBundle/Resources/public/js/messages.js',
		));

		return render_to_response('ZcoCoreBundle:Generator:delete.html.php', compact('title', 'description', 'message'));
	}

	/**
	 * Built-in batch delete action. Can be overwritten.
	 * @param array $pks		Les clés primaires des objets sélectionnés.
	 */
	protected function batchDelete(array $pks)
	{
		$config['actions'] = \Util::arrayDeepMerge(
			array(
				'_delete' => array('label' => 'Supprimer', 'route' => 'supprimer-%id%.html', 'credentials' => array()),
				'_edit'   => array('label' => 'Modifier', 'route' => 'modifier-%id%.html', 'icon' => '/bundles/zcocore/img/generator/edit.png', 'credentials' => array()),
				'_new'    => array('label' => 'Ajouter un nouvel élément', 'route' => 'ajouter.html', 'credentials' => array()),
				'_list'   => array('label' => 'Liste des éléments', 'route' => 'index-p%d.html', 'credentials' => array()),
			),
			isset($this->config['actions']) ? $this->config['actions'] : array()
		);

		$objects = \Doctrine_Query::create()
			->from($this->modelName)
			->whereIn('id', $pks)
			->execute();
		foreach($objects as $object)
		{
			$object->delete();
		}

		return redirect('Les éléments sélectionnés ont bien été supprimés.', sprintf($config['actions']['_list']['route'], 1), MSG_OK, 0);
	}

	########################
	##  INTERNAL METHODS  ##
	########################
	/**
	 * Generate the PHP code to display a field in the list page.
	 * @param string $name		Field name.
	 * @return string			PHP code.
	 */
	protected function renderField($name)
	{
		if (isset($this->fields[$name]))
		{
			$column = $this->fields[$name];

			//Boolean.
			if ($column->getType() == 'boolean')
			{
				return 'echo $row[\''.$column->getFieldName().'\'] ? \'<img src="/bundles/zcocore/img/generator/boolean-yes.png" alt="Oui" title="Oui" />\' : \'<img src="/bundles/zcocore/img/generator/boolean-no.png" alt="Non" title="Non" />\';';
			}
			//Date.
			elseif ($column->getType() == 'date')
			{
				return 'echo dateformat($row[\''.$column->getFieldName().'\'], DATE);';
			}
			//Timestamp.
			elseif ($column->getType() == 'timestamp')
			{
				return 'echo dateformat($row[\''.$column->getFieldName().'\'], DATETIME);';
			}
			//Longvarchar.
			elseif ($column->getType() == 'string' && ($column->getLength() > 255 || is_null($column->getLength())))
			{
				return 'echo nl2br(htmlspecialchars($row[\''.$column->getFieldName().'\']));';
			}
			//Enum.
			elseif ($column->getType() == 'enum')
			{
				return sprintf('echo htmlspecialchars($row[\'%s\']);',	$column->getFieldName());
			}
			//Foreign key.
			elseif ($column->isForeignKey())
			{
				return sprintf('echo method_exists($row->%s, \'__toString\') ? $row->%s : $row[\'%s\'];',
					$column->getForeignModel(),
					$column->getForeignModel(),
					$column->getFieldName()
				);
			}
			//General case: string.
			else
			{
				return 'echo htmlspecialchars($row[\''.$column->getFieldName().'\']);';
			}
		}
		//Callable.
		elseif (is_callable($name))
		{
			return sprintf('echo call_user_func(%s, $row);', var_export($name, true));
		}
		//Method of the model.
		elseif (is_string($name))
		{
			$method = 'get'.\Util_Inflector::camelize($name);
			return sprintf('echo method_exists($row, \'%s\') ? $row->%s() : \'Error: no field named %s.\';',
				$method,
				$method,
				$name
			);
		}
	}

	/**
	 * Transform an ordering expression to a SQL expression.
	 * @param string $field		Ordering.
	 * @return string			SQL.
	 */
	protected function getOrderBy($field)
	{
		if ($field[0] == '-')
		{
			return substr($field, 1).' DESC';
		}
		else
		{
			return $field;
		}
	}

	/**
	 * Get the form object associated with an object.
	 * @param array	$config					Configuration.
	 * @param Doctrine_Record|null $object	A model instance (for default values).
	 * @return Form
	 */
	protected function getForm(array $config, $object = null)
	{
		if (isset($config['form']['class']))
		{
			$class = $config['form']['class'];
			return new $class;
		}

		$form = new \Form;

		//If no configuration, all fields are added.
		if (empty($config['form']['fieldsets']))
		{
			foreach($this->fields as $name => $column)
			{
				if($column->isEditable())
				{
					$widget = !empty($config['form'][$name]['widget']) ? $config['form'][$name]['widget'] : null;
					$attrs = !empty($config['fields'][$name]['attributes']) ? $config['fields'][$name]['attributes'] : array();
					$label = !empty($config['fields'][$name]['label']) ? $config['fields'][$name]['label'] : Util_Inflector::humanize($name);
					$help = !empty($config['fields'][$name]['help']) ? $config['fields'][$name]['help'] : '';

					$form->addDoctrineField($column, $widget, array(), $attrs);
					$form->setLabel($name, $label);
					$form->setHelpText($name, $help);

					if (!is_null($object))
					{
						$form->setDefault($name, $object[$column->getFieldName()]);
					}
				}
			}
		}
		else
		{
			foreach($config['form']['fieldsets'] as $name => $params)
			{
				$classes = !empty($params['classes']) ? $params['classes'] : array();
				$description = !empty($params['description']) ? $params['description'] : '';
				$form->addFieldset($name, array('class' => $classes, 'description' => $description));

				//We append each field to the fieldset.
				foreach($params['display'] as $field)
				{
					$column = $this->fields[$field];
					$widget = !empty($config['form'][$field]['widget']) ? $config['form'][$field]['widget'] : null;
					$attrs = !empty($config['fields'][$field]['attributes']) ? $config['fields'][$field]['attributes'] : array();
					$label = !empty($config['fields'][$field]['label']) ? $config['fields'][$field]['label'] : \Util_Inflector::humanize($field);
					$help = !empty($config['fields'][$field]['help']) ? $config['fields'][$field]['help'] : '';
					$credentials = !empty($config['fields'][$field]['credentials']) ? verifier_array($config['fields'][$field]['credentials']) : true;

					if($credentials)
					{
						$form->addDoctrineField($column, $widget, array(), $attrs);
						$form->attachFieldset($field, $name);
						$form->setLabel($field, $label);
						$form->setHelpText($field, $help);

						if (!is_null($object))
						{
							$form->setDefault($field, $object[$column->getFieldName()]);
						}
					}
				}
			}
		}
		return $form;
	}

	/**
	 * Replace references to object attributes (%attr%) in a string.
	 * @param string $str
	 * @param Doctrine_Record $object		The model containing the attributes.
	 * @return string						Formatted string.
	 */
	protected function replaceReferences($str, \Doctrine_Record $object)
	{
		preg_match_all('`%([a-zA-Z0-9_-]+)%`', $str, $matches);
		foreach($matches[1] as $match)
		{
			$str = str_replace('%'.$match.'%', $object[$match], $str);
		}
		return $str;
	}
}

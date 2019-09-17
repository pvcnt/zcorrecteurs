<?php use Zco\Bundle\ContentBundle\Domain\CategoryDAO;
$Categories = CategoryDAO::ListerEnfants(CategoryDAO::GetIDCategorieCourante()); ?>
<div class="UI_box">
    <form method="post" action="<?php echo $view['router']->path('zco_blog_index') ?>">
		<label for="filtre" class="nofloat">Catégorie : </label>
		<select name="filtre" id="filtre"
		onchange="if (this.value == 0) document.location = '<?php echo $view['router']->path('zco_blog_index') ?>';
		          else document.location = '<?php echo $view['router']->path('zco_blog_index') ?>?filtre=' + this.value;">
			<option value="0" selected="selected">Tout le blog</option>
			<?php
			foreach($Categories as $c)
			{
				$marqueur = '';
				for($i = 1 ; $i < $c['cat_niveau'] ; $i++)
					$marqueur .= '.....';
				echo '<option value="'.$c['cat_id'].'"'.(isset($_GET['id']) && $_GET['id'] == $c['cat_id'] ? ' selected="selected"' : '').'>'.$marqueur.' '.htmlspecialchars($c['cat_nom']).'</option>';
			}
			?>
		</select>
		
		<noscript>
			<input type="submit" name="saut_rapide" value="Aller" />
		</noscript>
    
        <span style="margin-left: 40px;">
            Nous suivre : 
            <a href="<?php echo $view['router']->path('zco_blog_feed') ?>"><img src="/pix.gif" class="fff feed" alt="" /> flux RSS du blog</a> |
            <a href="https://twitter.com/zCorrecteurs"><img src="/img/oiseau_16px.png" alt="" /> Twitter</a> |
            <a href="https://www.facebook.com/pages/zCorrecteurs/292782574071649"><img src="/img/facebook.png" alt="" /> Facebook</a>
        </span>
    </form>
</div>

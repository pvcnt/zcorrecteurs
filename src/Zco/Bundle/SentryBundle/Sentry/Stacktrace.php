<?php

/**
 * Copyright (c) 2012 Sentry Team and individual contributors. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 *	1. Redistributions of source code must retain the above copyright notice, 
 *	   this list of conditions and the following disclaimer.
 *	2. Redistributions in binary form must reproduce the above copyright notice, 
 *	   this list of conditions and the following disclaimer in the documentation 
 *	   and/or other materials provided with the distribution.
 *	3. Neither the name of the Raven nor the names of its contributors may be 
 *	   used to endorse or promote products derived from this software without 
 *	   specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF 
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Zco\Bundle\SentryBundle\Sentry;

/**
 * Classe utilitaire servant à récupérer les informations des stacktraces.
 * Code modifié pour nos besoins propres.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Stacktrace
{
	public static function getStacktrace(array $stack)
	{
		$result = array();
		foreach ($stack as $frame)
		{
			if (!isset($frame['file']))
			{
				if (isset($frame['args']))
				{
					if (is_array($frame['args']))
					{
						$args = array();
						foreach ($frame['args'] as $arg)
						{
							$args[] = is_object($arg) ? get_class($arg) : (string) $arg;
						}
						$args = implode(',', $args);
					}
					elseif (is_object($frame['args']))
					{
						$args = get_class($frame['args']);
					}
					else
					{
						$args = (string) $frame['args'];
					}
				}
				else
				{
					$args = '';
				}
				if (isset($frame['class']))
				{
					$context['line'] = sprintf('%s%s%s(%s)', $frame['class'], $frame['type'], $frame['function'], $args);
				}
				else
				{
					$context['line'] = sprintf('%s(%s)', $frame['function'], $args);
				}
				$context['abs_path'] = '';
				$context['prefix'] = '';
				$context['suffix'] = '';
				$context['filename'] = $filename = '[Anonymous function]';
				$context['lineno'] = 0;
			}
			else
			{
				$context = self::readSourceFile($frame['file'], $frame['line']);
				$context['abs_path'] = $frame['file'];
				$filename = basename($frame['file']);
			}

			$module = $filename;
			if (isset($frame['class']))
			{
				$module .= ':' . $frame['class'];
			}

			array_push($result, array(
				'abs_path' => $context['abs_path'],
				'filename' => $context['filename'],
				'lineno' => $context['lineno'],
				'module' => $module,
				'function' => $frame['function'],
				'vars' => array(),
				'pre_context' => $context['prefix'],
				'context_line' => $context['line'],
				'post_context' => $context['suffix'],

			));
		}
		
		return array_reverse($result);
	}

	private static function readSourceFile($filename, $lineno)
	{
		$frame = array(
			'prefix'   => array(),
			'line'	 => '',
			'suffix'   => array(),
			'filename' => $filename,
			'lineno'   => $lineno,
		);

		if ($filename === null || $lineno === null)
		{
			return $frame;
		}

		// Code which is eval'ed have a modified filename.. Extract the
		// correct filename + linenumber from the string.
		$matches = array();
		$matched = preg_match("/^(.*?)\((\d+)\) : eval\(\)'d code$/", $filename, $matches);
		if ($matched)
		{
			$frame['filename'] = $filename = $matches[1];
			$frame['lineno'] = $lineno = $matches[2];
		}

		// Try to open the file. We wrap this in a try/catch block in case
		// someone has modified the error_trigger to throw exceptions.
		try
		{
			$fh = fopen($filename, 'r');
			if ($fh === false)
			{
				return $frame;
			}
		}
		catch (\ErrorException $exc)
		{
			return $frame;
		}

		$line = false;
		$cur_lineno = 0;

		while (!feof($fh))
		{
			$cur_lineno++;
			$line = fgets($fh);

			if ($cur_lineno == $lineno)
			{
				$frame['line'] = $line;
			}
			elseif ($lineno - $cur_lineno > 0 && $lineno - $cur_lineno < 3)
			{
				$frame['prefix'][] = $line;
			}
			elseif ($lineno - $cur_lineno > -3 && $lineno - $cur_lineno < 0)
			{
				$frame['suffix'][] = $line;
			}
		}
		fclose($fh);
		
		return $frame;
	}
}

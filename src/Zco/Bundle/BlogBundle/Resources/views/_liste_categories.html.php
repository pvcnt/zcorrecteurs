<?php use Zco\Bundle\ContentBundle\Domain\CategoryDAO;
$Categories = CategoryDAO::ListerEnfants(CategoryDAO::GetIDCategorieCourante()); ?>
<div class="UI_box">
    <form method="post" action="/blog/">
		<label for="cat" class="nofloat">Catégorie : </label>
		<select name="cat" id="cat"
		onchange="if(this.value == 0) document.location = '/blog/'; else document.location = 'categorie-'+this.value+'.html';">
			<option value="0" selected="selected">Tout le blog</option>
			<?php
			foreach($Categories as $c)
			{
				$marqueur = '';
				for($i = 1 ; $i < $c['cat_niveau'] ; $i++)
					$marqueur .= '.....';
				echo '<option value="'.$c['cat_id'].'"'.($_GET['id'] == $c['cat_id'] ? ' selected="selected"' : '').'>'.$marqueur.' '.htmlspecialchars($c['cat_nom']).'</option>';
			}
			?>
		</select>
		
		<noscript>
			<input type="submit" name="saut_rapide" value="Aller" />
		</noscript>
    
        <span style="margin-left: 40px;">
            Nous suivre : 
            <a href="/blog/flux.html"><img src="/pix.gif" class="fff feed" alt="" /> flux RSS du blog</a><?php if (isset($categorieId)){ ?>, <a href="/blog/flux-<?php echo $categorieId ?>.html">de cette catégorie</a><?php } ?> | 
            <a href="https://twitter.com/zCorrecteurs"><img src="/img/oiseau_16px.png" alt="" /> Twitter</a> |
            <a href="https://www.facebook.com/pages/zCorrecteurs/292782574071649"><img src="/img/facebook.png" alt="" /> Facebook</a>
        </span>
    </form>
</div>

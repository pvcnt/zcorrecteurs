<?php if ($pageCount > 1): ?>
<div class="pagination">
    <ul>
        <?php if (isset($first) && $current != $first): ?>
            <li class="first">
                <a href="<?php echo $view['router']->generate($route, array_merge($query, array($pageParameterName => $first))) ?>">««</a>
            </li>
        <?php endif ?>

        <?php if (isset($previous)): ?>
            <li class="previous">
                <a href="<?php echo $view['router']->generate($route, array_merge($query, array($pageParameterName => $previous))) ?>">«</a>
            </li>
        <?php endif ?>

        <?php foreach ($pagesInRange as $page): ?>
            <?php if ($page != $current): ?>
                <li>
                    <a href="<?php echo $view['router']->generate($route, array_merge($query, array($pageParameterName => $page))) ?>"><?php echo $page ?></a>
                </li>
            <?php else: ?>
                <li class="active"><a href="#"><?php echo $page ?></a></li>
            <?php endif ?>
        <?php endforeach ?>

        <?php if (isset($next)): ?>
            <li class="next">
                <a href="<?php echo $view['router']->generate($route, array_merge($query, array($pageParameterName => $next))) ?>">»</a>
            </li>
        <?php endif ?>

        <?php if (isset($last) && $current != $last): ?>
            <li class="last">
                <a href="<?php echo $view['router']->generate($route, array_merge($query, array($pageParameterName => $last))) ?>">»»</a>
            </li>
        <?php endif ?>
    </ul>
</div>
<?php endif ?>
<ul class="navbar-nav me-auto mb-2 mb-lg-0 navbar-menu">
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="<?= base_href('/') ?>"><?php _e('tpl_menu_1') ?></a>
    </li>
    <?php if (check_auth()): ?>
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?= base_href('/dashboard') ?>"><?php _e('tpl_menu_2') ?></a>
        </li>
    <?php else: ?>
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?= base_href('/register') ?>"><?php _e('tpl_menu_3') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?= base_href('/login') ?>"><?php _e('tpl_menu_4') ?></a>
        </li>
    <?php endif; ?>
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="<?= base_href('/users') ?>"><?php _e('tpl_menu_5') ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="<?= base_href('/posts') ?>"><?php _e('tpl_menu_6') ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="<?= base_href('/contact') ?>"><?php _e('tpl_menu_7') ?></a>
    </li>
</ul>

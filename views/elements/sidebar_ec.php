<?php
use Adapter\Globals\ServiceConstant;
use yii\helpers\Html;
use yii\helpers\Url;
use Adapter\Util\Calypso;

?>

<div id="nav-col">
    <section id="col-left" class="col-left-nano">
        <div id="col-left-inner" class="col-left-nano-content">
            <div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
                <div>&nbsp;</div>
                <ul class="nav nav-pills nav-stacked">
                    <li class="nav-header nav-header-first hidden-sm hidden-xs">

                    </li>
                    <?php
                    $permission = Calypso::getInstance()->permissionMap();
                    $menus = Calypso::getInstance()->getMenus();
                    $role = $session_data['role']['id'];
                    $branch = Calypso::getValue($session_data, 'branch.branch_type');
                    if(empty($branch) && Calypso::isCooperateUser()){
                        $branch = ServiceConstant::BRANCH_TYPE_COMPANY;
                    }

                    foreach ($menus as $k => $v) {
                        if (isset($v['base']) && !Calypso::canAccess($role, $v['base'] . '/*')) {
                            continue;
                        }
                        if (isset($v['branch']) && !in_array($branch, $v['branch'])) {
                            continue;
                        }

                        //check user type
                        if(isset($v['user_type']) && !in_array($role, $v['user_type'])){
                            continue;
                        }
                        if (in_array($role, [ServiceConstant::USER_TYPE_COMPANY_OFFICER, ServiceConstant::USER_TYPE_COMPANY_ADMIN]) && !(Calypso::getValue($v, 'corporate', false))){
                            continue;
                        }

                        //get top css class

                        $topClass = '';
                        $style = '';
                        if(Calypso::isActiveMenu($v)){
                            if(!is_array($v['base_link'])){
                                $topClass = "class='active'";
                            }else{
                                $topClass="class='open'";
                                $style = 'style="display: block;"';
                            }
                        }

                        ?>
                        <li <?= $topClass ?>>
                            <a href="<?= !is_array($v['base_link']) ? Url::toRoute(['/' . $v['base_link']]) : '#' ?>"
                               class="<?php echo is_array($v['base_link']) ? 'dropdown-toggle' : '' ?>">
                                <i class="<?= $v['class']; ?>"></i>
                                <span><?= Calypso::getInstance()->normaliseLinkLabel($k); ?></span>
                                <?php
                                if (is_array($v['base_link'])) {
                                    ?>
                                    <i class="fa fa-angle-right drop-icon"></i>
                                <?php } ?>
                            </a>
                            <?php
                            if (isset($v['base_link']) && is_array($v['base_link'])) {
                                ?>
                                <ul class="submenu" <?=$style?>>
                                    <?php
                                    foreach ($v['base_link'] as $key => $value) {
                                        if (isset($value['base']) && !Calypso::canAccess($role, $value['base'] . '/*')) {
                                            continue;
                                        }
                                        if (isset($value['base_link']) && !is_array($value['base_link']) && !Calypso::canAccess($role, $value['base_link'])) {
                                            continue;
                                        }
                                        if (isset($value['branch']) && !in_array($branch, $value['branch'])) {
                                            continue;
                                        }
                                        //check user type
                                        if(isset($value['user_type']) && !in_array($role, $value['user_type'])){
                                            continue;
                                        }

                                        $innerClass = '';
                                        $innerStyle = '';
                                        if(Calypso::isActiveMenu($value)){
                                            if(!is_array($value['base_link'])){
                                                $innerClass = "class='active'";
                                            }else{
                                                $innerClass="class='open'";
                                                $innerStyle = 'style="display: block;"';
                                            }
                                        }

                                        if (isset($value['base_link']) && !is_array($value['base_link'])) {
                                            ?>
                                            <li <?= $innerClass ?> >
                                                <a href="<?= Url::toRoute(["/" . $value['base_link']]) ?>">
                                                    <i class="<?= $value['class'] ?>"></i>
                                                    <span><?= Calypso::getInstance()->normaliseLinkLabel($key); ?></span></a>
                                            </li>
                                        <?php } else {

                                            ?>
                                            <li <?= $innerClass ?> >
                                                <a href="#" class="dropdown-toggle">
                                                    <i class="<?= $value['class']; ?>"></i>
                                                    <span><?= Calypso::getInstance()->normaliseLinkLabel($key); ?></span>
                                                    <?php
                                                    if (is_array($value['base_link'])) {
                                                        ?>
                                                        <i class="fa fa-angle-right drop-icon"></i>
                                                    <?php } ?>
                                                </a>
                                                <ul class="submenu" <?=$innerStyle?>>
                                                    <?php
                                                    if (isset($value['base_link'])) {
                                                        foreach ($value['base_link'] as $subkey => $subvalue) {
                                                            if (isset($subvalue['base']) && !Calypso::canAccess($role, $subvalue['base'] . '/*')) {
                                                                continue;
                                                            }
                                                            if (isset($subvalue['base_link']) && !is_array($subvalue['base_link']) && !Calypso::canAccess($role, $subvalue['base_link'])) {
                                                                continue;
                                                            }
                                                            if (isset($subvalue['branch']) && !in_array($branch, $subvalue['branch'])) {
                                                                continue;
                                                            }
                                                            //check user type
                                                            if(isset($subvalue['user_type']) && !in_array($role, $subvalue['user_type'])){
                                                                continue;
                                                            }
                                                            if (isset($subvalue['base_link']) && !is_array($subvalue['base_link'])) {
                                                                ?>
                                                                <li <?= Calypso::isActiveMenu($subvalue)?"class='active'":''?>>
                                                                    <a href="<?= Url::toRoute(['/' . $subvalue['base_link']]) ?>">
                                                                        <i class="<?= $subvalue['class'] ?>"></i>
                                                                        <span><?= Calypso::getInstance()->normaliseLinkLabel($subkey); ?></span></a>
                                                                </li>
                                                            <?php }
                                                        }
                                                    } ?>
                                                </ul>
                                            </li>

                                            <?php

                                        }
                                    } ?>
                                </ul>
                            <?php } ?>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    </section>
    <div id="nav-col-submenu"></div>
</div>


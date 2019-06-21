<?php
$this->addJS('templates/default/js/jquery-cookie.js');
$this->addJS('templates/default/js/datatree.js');
$this->addCSS('templates/default/css/datatree.css');
$this->addJS('templates/default/js/admin-content.js');

$this->setPageTitle('Дисконт-коды');

$this->addBreadcrumb('Дисконт-коды', $this->href_to('discount'));

$this->addToolButton(array(
    'class' => 'settings',
    'title' => LANG_CONFIG,
    'href'  => $this->href_to('controllers', array('edit', 'users'))
));

$this->addToolButton(array(
    'class' => 'add',
    'title' => LANG_CP_USER_ADD,
    'href'  => $this->href_to('users', 'add')
));

$this->addToolButton(array(
    'class' => 'add_folder',
    'title' => LANG_CP_USER_GROUP_ADD,
    'href'  => $this->href_to('users', 'group_add')
));

$this->addToolButton(array(
    'class' => 'edit',
    'title' => LANG_CP_USER_GROUP_EDIT,
    'href'  => $this->href_to('users', 'group_edit')
));

$this->addToolButton(array(
    'class' => 'permissions',
    'title' => LANG_CP_USER_GROUP_PERMS,
    'href'  => $this->href_to('users', 'group_perms')
));

$this->addToolButton(array(
    'class' => 'delete',
    'title' => LANG_CP_USER_GROUP_DELETE,
    'href'  => $this->href_to('users', 'group_delete'),
    'onclick' => "return confirm('".LANG_CP_USER_GROUP_DELETE_CONFIRM."')"
));

$this->addToolButton(array(
    'class' => 'excel show_on_nonempty',
    'title' => LANG_LIST_EXPORT,
    'href'  => '#',
    'target' => '_blank',
));

$this->addToolButton(array(
    'class' => 'help',
    'title' => LANG_HELP,
    'target' => '_blank',
    'href'  => LANG_HELP_URL_USERS
));

?>

<h1>Дисконт-коды</h1>

<table class="layout">
    <tr>
        <td class="sidebar" valign="top">

            <div id="datatree">
                <ul id="treeData" style="display: none">
                    <?php foreach($groups as $id=>$group){ ?>
                        <li id="<?php echo $group['id'];?>" class="folder"><?php echo $group['title']; ?></li>
                    <?php } ?>
                </ul>
            </div>

            <script type="text/javascript">
                $(function(){
                    $(document).on('click', '.datagrid .filter_ip', function (){
                        $('#filter_ip').val($(this).text()).trigger('input');
                        return false;
                    });

                    $('.cp_toolbar .delete_filter a').hide();
                    $("#datatree").dynatree({

                        onPostInit: function(isReloading, isError){
                            var path = $.cookie('icms[users_tree_path]');
                            if (!path) { path = '/0'; }
                            $("#datatree").dynatree("getTree").loadKeyPath(path, function(node, status){
                                if(status == "loaded") {
                                    node.expand();
                                }else if(status == "ok") {
                                    node.activate();
                                    node.expand();
                                    //icms.datagrid.init();

                                    var sb = $('.sidebar');
                                    $(sb).after('<td id="slide_cell"></td>');
                                    $('#slide_cell').on('click', function (){
                                        if($(sb).is(':visible')){
                                            $(sb).hide();
                                            $(this).addClass('unslided');
                                        } else {
                                            $(sb).show();
                                            $(this).removeClass('unslided');
                                        }
                                    });
                                    $(window).on('resize', function(){
                                        if(!$(sb).is(':visible')){
                                            $('#slide_cell').addClass('unslided');
                                        }
                                    }).triggerHandler('resize');
                                }
                            });
                        },

                        onActivate: function(node){
                            node.expand();
                            $.cookie('icms[users_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
                            var key = node.data.key;
                            //icms.datagrid.setURL("<?php echo $this->href_to('users'); ?>/" + key);
                            $('.cp_toolbar .filter a').attr('href', "<?php echo $this->href_to('users', array('filter')); ?>/" + key[0]);
                            $('.cp_toolbar .add a').attr('href', "<?php echo $this->href_to('users', 'add'); ?>/" + key);
                            $('.cp_toolbar .transfer a').attr('href', "<?php echo $this->href_to('controllers', array('edit', 'messages', 'pmailing')); ?>/" + key);
                            if (key == 0){
                                $('.cp_toolbar .edit a').hide();
                                $('.cp_toolbar .permissions a').hide();
                                $('.cp_toolbar .delete a').hide();
                            } else {
                                $('.cp_toolbar .edit a').show().attr('href', "<?php echo $this->href_to('users', 'group_edit'); ?>/" + key);
                                $('.cp_toolbar .permissions a').show().attr('href', "<?php echo $this->href_to('users', 'group_perms'); ?>/" + key);
                                $('.cp_toolbar .delete a').show().attr('href', "<?php echo $this->href_to('users', 'group_delete'); ?>/" + key + '?csrf_token='+icms.forms.getCsrfToken());
                            }
                            //icms.datagrid.loadRows();
                        }

                    });
                });
            </script>

        </td>
        <td class="main" valign="top">
            <div class="cp_toolbar">
                <?php /** @var cmsTemplate $this */
                $this->toolbar(); ?>
            </div>
            <?php

            /** @var s4y\grid\Grid $grid */
            $gridStr = $grid->render();
            s4y\Assets::addStyle('display:none', '.s4y-grid-action-btn');

            $this->addHead(s4y\Assets::getCss());
            $this->addOutput(s4y\Assets::getJs());

            echo $gridStr;
            ?>


        </td>
    </tr>
</table>



<?php include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/' . ADMIN_PANEL . '/guard.php';
include $_SERVER['DOCUMENT_ROOT'] . '/' . ADMIN_PANEL . '/includes.php';
include 'config.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/' . ADMIN_PANEL . '/template/head.php' ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed <?php echo $adminTheme['body_class'] ?>">
<div class="wrapper">
    <? ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/' . ADMIN_PANEL . '/template/header.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <?$db_element = mysqli_query($db, "SELECT * FROM `" . $_params['table'] . "` WHERE id='" . $id . "'");
    if ($ElemParent = mysqli_fetch_assoc($db_element)) {?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?=$_params['title']?></h1>
                        <a href="index.php" class="btn btn-info mt-2">Назад</a>
                        <a href="#" class="btn btn-info mt-2" id="reload-link">Отменить</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <? if( isset( $_POST['ok'] )  && $Admin->CheckPermission($_params['access_edit']) ){
            $ar_clean = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $code = str_replace(" ", "_", $ar_clean['code']);
            $code = $Router->translitURL($code);
            $code = strtoupper($code);

            $is_el = mysqli_query($db, "SELECT * FROM `".$_params['table']."` WHERE `code`='".$code."' ");
            $el_count = mysqli_num_rows($is_el);

            if( $el_count > 0 ){
                ?>
                <div class="alert alert-danger"> <?=$GLOBALS['CPLANG']['CODE']?> <?=$code?> <?=$GLOBALS['CPLANG']['EXISTS_WORD']?> </div>
                <?
                $el = mysqli_fetch_assoc( $is_el );
                ?>
                <div class="codeBlock">
                    <div id="<?=uniqid()?>" class="php_code" style="padding: 5px;font-family: Consolas, Courier">
                        <?echo"$";echo"GLOBALS['dictionary']['".$el['code']."']<br>";?>
                    </div>

                    <div id="<?=uniqid()?>" class="html_code" style="padding: 5px;font-family: Consolas, Courier">
                        <?echo"&lt;?=$";echo"GLOBALS['dictionary']['".$el['code']."']?&gt;<br>";?>
                    </div>

                    <br>
                    <div style="position: absolute;bottom: -5px;" class="notific">  </div>
                </div>
                <?
            }else{

                addElement($_params['table'], array('code'=>$code,'section_id'=>$id));

                ?>
                <div class="codeBlock">
                    <div id="<?=uniqid()?>" class="php_code" style="padding: 5px;font-family: Consolas, Courier">
                        <?echo"$";echo"GLOBALS['dictionary']['".$code."']<br>";?>
                    </div>

                    <div id="<?=uniqid()?>" class="html_code" style="padding: 5px;font-family: Consolas, Courier">
                        <?echo"&lt;?=$";echo"GLOBALS['dictionary']['".$code."']?&gt;<br>";?>
                    </div>

                    <br>
                    <div style="position: absolute;bottom: -5px;" class="notific">  </div>
                </div>
                <?
            }

            //addElement($_params['table']); show(mysql_error());
        }elseif (isset($_POST['ok']) && !$Admin->CheckPermission($_params['access_edit'])){?>
            <div class="alert alert-danger">У Вас нет прав доступа на редактирование данного раздела</div>
        <?}
        ?>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <form method="post" enctype="multipart/form-data" class="card" onsubmit="return codevalidate()">
                    <div class="tab-content card-body" id="custom-content-above-tabContent">
                        <div class="tab-pane fade show active" id="tab_1" role="tabpanel" aria-labelledby="tab_1">
                            <div class="row form-group">
                                <div class="col-lg-2 col-xs-12">
                                    <b> Код <span class="red">*</span></b>
                                </div>
                                <div class="col-lg-8 col-xs-12">
                                    <input required name="code" id="code" class="form-control" value="MSG_<?= $ElemParent['code'] ?>_" />
                                </div>
                            </div>
                            <? foreach ($Admin->langs as $keyLang => $LangInfo) { ?>
                                <div class="row form-group">
                                    <div class="col-lg-2 col-xs-12">
                                        <b><?= $LangInfo['title'] ?> <span class="red">*</span></b>
                                    </div>
                                    <div class="col-lg-8 col-xs-12">
                                        <textarea required class="form-control" name="title_<?= $keyLang ?>"></textarea>
                                    </div>
                                </div>
                            <? } ?>
                            <div class="row form-group">
                                <div class="col-lg-2 col-xs-12">
                                    <b> Комментарий </b>
                                </div>
                                <div class="col-lg-8 col-xs-12">
                                    <textarea class="form-control" name="comments"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" style="text-align: center">
                        <input type="submit" class="btn btn-success btn-lg" value="Сохранить" name="ok"/>
                    </div>
                </form>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <?}?>
</div>
<!-- ./wrapper -->
<?php include $_SERVER['DOCUMENT_ROOT'] . '/' . ADMIN_PANEL . '/template/footer_scripts.php' ?>
<script>
    $('.txt_editor').summernote();
</script>
</body>
</html>
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
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/' . ADMIN_PANEL . '/template/header.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?=$_params['title']?></h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <? if (isset($_POST['ok']) && $Admin->CheckPermission($_params['access_edit'])) {
            $ar_clean = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            foreach ($Admin->langs as $key => $value) {
                $txt[] = 'text1_' . $value['code'];
                $txt[] = 'text2_' . $value['code'];
                $txt[] = 'text3_' . $value['code'];
                $txt[] = 'text4_' . $value['code'];
            }

            ini_set('error_reporting', E_ALL);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);

            $exceptions[] = 'oldimg';

            if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name'] != '') {
                include($_SERVER['DOCUMENT_ROOT'] . '/' . ADMIN_PANEL . '/engine/CImageProcessor.php');
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                if ($extension == 'svg'){
                    $FileName = generateName(10) . '.svg';
                }else{
                    $FileName = generateName(10) . '.webp';
                }
                $inputFile = $_FILES['image'];
                $outputPath = $_SERVER['DOCUMENT_ROOT'] . '/'.$_params['image'].$FileName;
                $imageProcessor = new ImageProcessor($inputFile, $outputPath, $_params['image_width'], $_params['image_height']);
                $imageProcessor->processImage();
            }else{
                $FileName = $ar_clean['oldimg'];
            }

            updateElement($id, $_params['table'], array('image'=>$FileName), $txt, array(), $exceptions);
        }elseif (isset($_POST['ok']) && !$Admin->CheckPermission($_params['access_edit'])){?>
            <div class="alert alert-danger">У Вас нет прав доступа на редактирование данного раздела</div>
        <?}

        $db_element = mysqli_query($db, "SELECT * FROM `".$_params['table']."` WHERE id='".$id."'");
        $Elem = mysqli_fetch_array($db_element);
        ?>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <form method="post" enctype="multipart/form-data" class="card">
                    <ul class="nav nav-tabs card-header" id="custom-content-above-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-content-above-home-tab" data-toggle="pill"
                               href="#tab_1" role="tab" aria-controls="tab_1" aria-selected="true">Общие данные</a>
                        </li>
                    </ul>
                    <div class="tab-content card-body" id="custom-content-above-tabContent">
                        <div class="tab-pane fade show active" id="tab_1" role="tabpanel" aria-labelledby="tab_1">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="col-sm-3">Текущее изображение</label>
                                <div class="col-sm-10">
                                    <img style="max-width: 300px;" src="<?=$_params['image'].$Elem['image']?>" />
                                    <input type="hidden" name="oldimg" value="<?=$Elem['image']?>" />
                                </div>
                            </div>
                            <?php
                            editElem('image', 'Изменить изображение (' . $_params['image_width'] . ' X ' . $_params['image_height'] . ')', '5', $Elem, '', 'edit', 0, 6, '', '');

                            foreach ($Admin->langs as $lang_index) {
                                $lang_code = $lang_index['code'];
                                editElem('title', 'Заголовок (' . $lang_code . ')', '1', $Elem, $lang_code, 'edit', 1, 7);
                                editElem('text1', 'Текст (' . $lang_code . ')', '1', $Elem, $lang_code, 'edit', 0, 7);
                                editElem('text2', 'Текст 2 (' . $lang_code . ')', '1', $Elem, $lang_code, 'edit', 0, 7);
                                editElem('text3', 'Текст 3 (' . $lang_code . ')', '1', $Elem, $lang_code, 'edit', 0, 7);
                                editElem('text4', 'Текст 4 (' . $lang_code . ')', '1', $Elem, $lang_code, 'edit', 0, 7);
                            }

                            editElem('number1', 'Цифра 1', '1', $Elem, '', 'edit', 0, 7);
                            editElem('number2', 'Цифра 2', '1', $Elem, '', 'edit', 0, 7);
                            editElem('number3', 'Цифра 3', '1', $Elem, '', 'edit', 0, 7);
                            editElem('number4', 'Цифра 4', '1', $Elem, '', 'edit', 0, 7);
                            ?>
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
</div>
<!-- ./wrapper -->
<?php include $_SERVER['DOCUMENT_ROOT'] . '/' . ADMIN_PANEL . '/template/footer_scripts.php' ?>
<script>
    $('.txt_editor').summernote();
</script>
</body>
</html>
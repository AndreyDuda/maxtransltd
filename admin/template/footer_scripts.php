<!-- jQuery -->
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/moment/moment.min.js"></script>
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- select2 -->
<script src="/<?php echo ADMIN_PANEL?>/template/plugins/select2/js/select2.full.min.js"></script>
<!-- AdminLTE App -->
<script src="/<?php echo ADMIN_PANEL?>/template/dist/js/adminlte.js"></script>

<script>
    function initLoader() {
        let loader = document.createElement("div");
        loader.classList.add("loader");
        let loaderIcon = document.createElement("i");
        loaderIcon.className = "fas fa-3x fa-sync-alt fa-spin";
        loader.append(loaderIcon);
        document.querySelector("body").prepend(loader);
    };

    function removeLoader() {
        document.querySelector(".loader").remove();
    };

    $('.select2').select2();
  function toggleDarkMode(item){
    $(item).find('i').toggleClass('far').toggleClass('fas');
    $('body').toggleClass('dark-mode');
    $('nav.main-header').toggleClass('navbar-dark').toggleClass('navbar-light').toggleClass('navbar-white');
    $('aside').toggleClass('sidebar-light-primary').toggleClass('sidebar-dark-info');
    $('aside nav').toggleClass('navbar-dark');
    $.ajax({
      type:'post',
      url:'/<?php echo ADMIN_PANEL?>/ajax.php',
      data:{
        'request':'change_theme'
      },
      success:function(answer){
        if ($.trim(answer) != 'ok'){
          alert('Ошибка!');
        }
      }
    })
  };

  function checkPassword(field) {
      var password = $.trim( $(field).val() ); // Получаем пароль из формы
      var s_letters = "qwertyuiopasdfghjklzxcvbnm"; // Буквы в нижнем регистре
      var b_letters = "QWERTYUIOPLKJHGFDSAZXCVBNM"; // Буквы в верхнем регистре
      var digits = "0123456789"; // Цифры
      var specials = "!@#$%^&*()_-+=\|/.,:;[]{}"; // Спецсимволы
      var is_s = false; // Есть ли в пароле буквы в нижнем регистре
      var is_b = false; // Есть ли в пароле буквы в верхнем регистре
      var is_d = false; // Есть ли в пароле цифры
      var is_sp = false; // Есть ли в пароле спецсимволы
      for (var i = 0; i < password.length; i++) {
          /* Проверяем каждый символ пароля на принадлежность к тому или иному типу */
          if (!is_s && s_letters.indexOf(password[i]) != -1) is_s = true;
          else if (!is_b && b_letters.indexOf(password[i]) != -1) is_b = true;
          else if (!is_d && digits.indexOf(password[i]) != -1) is_d = true;
          else if (!is_sp && specials.indexOf(password[i]) != -1) is_sp = true;
      }
      var rating = 0;
      var text = "";
      if (is_s) rating++; // Если в пароле есть символы в нижнем регистре, то увеличиваем рейтинг сложности
      if (is_b) rating++; // Если в пароле есть символы в верхнем регистре, то увеличиваем рейтинг сложности
      if (is_d) rating++; // Если в пароле есть цифры, то увеличиваем рейтинг сложности
      if (is_sp) rating++; // Если в пароле есть спецсимволы, то увеличиваем рейтинг сложности
      /* Далее идёт анализ длины пароля и полученного рейтинга, и на основании этого готовится текстовое описание сложности пароля */
      if (password.length < 6 && rating < 3) text = "Легкий";
      else if (password.length < 6 && rating >= 3) text = "Средний";
      else if (password.length >= 8 && rating < 3) text = "Средний";
      else if (password.length >= 8 && rating >= 3) text = "Сложный";
      else if (password.length >= 6 && rating == 1) text = "Легкий";
      else if (password.length >= 6 && rating > 1 && rating < 4) text = "Средний";
      else if (password.length >= 6 && rating == 4) text = "Сложный";

      if (password.length < 1) text = "Очень легкий";
      $('#indicator').text( text );
  };

  function refresh_elem(id, table){
      $.ajax({
          type: "POST",
          url: "/<?=ADMIN_PANEL?>/ajax.php",
          data: "request=refresh&id="+id+"&table="+table,
          success: function(msg){
              location.reload();
          }
      })
  };

  function codevalidate(){
      var code = $('#code').val();
      var a = code.toUpperCase();
      $('#code').val(a);
      return true;
  }
    var reloadLink = document.getElementById('reload-link');

    // Проверка наличия ссылки
    if (reloadLink) {
        // Добавление обработчика события
        reloadLink.addEventListener('click', function(event) {
            event.preventDefault();
            location.reload(true);
        });
    }
</script>

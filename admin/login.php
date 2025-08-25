<?php require_once('../initialize.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition login-page  dark-mode">
  <script>
    start_loader()
  </script>
<div class="login-box">
  <div class="text-center mb-3">
    <img src="images/administrator.png" alt="Administrator" style="max-width: 120px; width: 100%; height: auto;">
  </div>
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="./" class="h1"><b>Login</b></a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form id="login-frm" action="" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" placeholder="Username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <a href="<?php echo base_url ?>">Go to Website</a>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <!-- /.social-auth-links -->

      <!-- <p class="mb-1">
        <a href="forgot-password.html">I forgot my password</a>
      </p> -->
      
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function(){
    end_loader();
  })
</script>
<script>
  // Add the missing login form handling
  $(function(){
    $('#login-frm').submit(function(e){
      e.preventDefault();
      var _this = $(this);
      if($('.err-msg').length > 0)
        $('.err-msg').remove();
      start_loader();
      $.ajax({
        url: '../classes/Login.php?f=login',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        error: function(err){
          console.log(err);
          alert_toast("An error occurred", 'error');
          end_loader();
        },
        success: function(resp){
          if(typeof resp == 'object' && resp.status == 'success'){
            alert_toast("Login Successfully", 'success');
            setTimeout(function(){
              location.href = './';
            }, 2000);
          }else if(resp.status == 'incorrect'){
            var _err_el = $('<div>');
            _err_el.addClass("alert alert-danger err-msg").text("Incorrect Credentials.");
            _this.prepend(_err_el);
            _err_el.show('slow');
            end_loader();
          }else{
            console.log(resp);
            alert_toast("An error occurred", 'error');
            end_loader();
          }
        }
      })
    })
  })
</script>
</body>
</html>
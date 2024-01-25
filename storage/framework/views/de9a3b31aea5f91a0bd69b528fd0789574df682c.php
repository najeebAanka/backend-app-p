  <!-- Vendor JS Files -->
  <script src="<?=url('')?>/dashboard/assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="<?=url('')?>/dashboard/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?=url('')?>/dashboard/assets/vendor/chart.js/chart.umd.js"></script>
  <script src="<?=url('')?>/dashboard/assets/vendor/echarts/echarts.min.js"></script>
  <script src="<?=url('')?>/dashboard/assets/vendor/quill/quill.min.js"></script>
  <script src="<?=url('')?>/dashboard/assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="<?=url('')?>/dashboard/assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="<?=url('')?>/dashboard/assets/vendor/php-email-form/validate.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
  <!-- Template Main JS File -->
  <script src="<?=url('')?>/dashboard/assets/js/main.js"></script>
  <script type="text/javascript">
  let server = "<?php echo e(url('')); ?>";
$(document).ready(function () {
    
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});




function startTime() {
  const today = new Date();
  let h = today.getHours();
  let m = today.getMinutes();
  let s = today.getSeconds();
  m = checkTime(m);
  s = checkTime(s);
  document.getElementById('nav-clock').innerHTML =  "Local time : " + h + ":" + m + ":" + s;
  setTimeout(startTime, 1000);
}

function checkTime(i) {
  if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
  return i;
}
startTime();



});
</script>
  
<?php /**PATH C:\wamp64\www\test\resources\views/dashboard/shared/js.blade.php ENDPATH**/ ?>
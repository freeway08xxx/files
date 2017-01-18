<script>
// ss本体のconst
var ss = angular.module('ss');
ss.constant('ssConst', JSON.parse('<?= json_encode($ss_const); ?>'));

// appのconst
var app = angular.module(window.ssContentApp);
app.constant('appConst', JSON.parse('<?= json_encode($app_const); ?>'));
</script>

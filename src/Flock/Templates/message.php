<?php
$now = new DateTime();
?>

<h2>The tests are done!</h2>


<p><?= $custom ?></p>
<p></p>
<p>This message was generated at <?= $now->format('d/m/Y H:i:s'); ?>.</p>


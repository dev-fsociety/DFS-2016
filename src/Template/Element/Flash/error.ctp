<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="alert callout" data-closable>
	<p><?= $message ?></p>
	<button class="close-button" aria-label="Dismiss alert" type="button" data-close>
		<span aria-hidden="true">&times;</span>
	</button>
</div>
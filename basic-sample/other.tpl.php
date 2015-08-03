<? core::prepend('head','title','Another page - ConKit test') ?>

<h1>Hello again</h1>
This is just another sample page. But it has some business logic done behind.
It retrives latest exchange rate. See <i>other.mod.php</i> for that.

<h2>Currency Exchange Rates on <?=core::reg('rates')->date?></h2>
<h4>Comparing to <?=core::reg('rates')->base?></h4>

<div style="-webkit-column-count:3; -moz-column-count:3; column-count:3;">
	<? foreach (get_object_vars(core::reg('rates')->rates) as $symbol=>$rate): ?>
		<div>
			<?=$symbol?> -- <?=$rate?>
		</div>
	<? endforeach ?>
</div>


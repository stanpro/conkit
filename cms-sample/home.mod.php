<?

$block= file(core::config('data-path').'block1.txt');
core::reg('headline1', $block[0]);
core::reg('subheadline1', $block[1]);
unset($block[0]);
unset($block[1]);
core::reg('text1', '<p>'.implode('</p><p>',$block).'</p>');


$block= file(core::config('data-path').'block2.txt');
core::reg('headline2', $block[0]);
core::reg('subheadline2', $block[1]);
unset($block[0]);
unset($block[1]);
core::reg('text2', '<p>'.implode('</p><p>',$block).'</p>');

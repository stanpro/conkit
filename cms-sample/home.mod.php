<?

$block= file(core::config('data-path').'block1.txt');
core::vars('headline1', $block[0]);
core::vars('subheadline1', $block[1]);
unset($block[0]);
unset($block[1]);
core::vars('text1', '<p>'.implode('</p><p>',$block).'</p>');


$block= file(core::config('data-path').'block2.txt');
core::vars('headline2', $block[0]);
core::vars('subheadline2', $block[1]);
unset($block[0]);
unset($block[1]);
core::vars('text2', '<p>'.implode('</p><p>',$block).'</p>');

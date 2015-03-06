<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$tmpQueryString = str_replace("&", "&amp;", $_SERVER['QUERY_STRING']);
?>
    <comments>
        <comment_writable><?=$is_comment_write?"true":"false"?></comment_writable>
<?php
$stackPnt = 0;

$depthBefore = 0;
$depthCurrent = 0;
$indentDefault = "        ";
for ($i = 0; $i < count($list); $i++){
    $item = $list[$i];

    $tmpContent = $item['content'];
    $tmpContent = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp|mms)\:\/\/([^[:space:]]+)\.(mp3|wma|wmv|asf|asx|mpg|mpeg)\".*\<\/a\>\]/i", "<script>doc_write(obj_movie('$1://$2.$3'));</script>", $tmpContent);

    if($i < count($list)){
        $depthNext = strlen($list[$i+1]['wr_comment_reply']);
    } else {
        $depthNext = null;
    }

    if($i != 0 && $depthCurrent <= $depthBefore) {
        $tmpIndent = $indentDefault;
        for($j = 0; $j < $stackPnt; $j++){
            $tmpIndent .= "        ";
        }

?>
<?=$tmpIndent?></comment>
<?php
    }

    if($depthCurrent < $depthBefore){
        for($j = 0; $j < ($depthBefore - $depthCurrent); $j++){
            $tmpIndent = $indentDefault;
            for($k = 0; $k < $stackPnt - 1; $k++){
                $tmpIndent .= "        ";
            }

?>
<?=$tmpIndent?>    </comments>
<?=$tmpIndent?></comment>        
<?php
            $stackPnt--;
        }
    }

    $tmpIndent = $indentDefault;
    for($j = 0; $j < $stackPnt; $j++){
        $tmpIndent .= "        ";
    }
?>
<?=$tmpIndent?><comment>
<?=$tmpIndent?>    <id><?=$item['wr_id']?></id>
<?=$tmpIndent?>    <name><?=$item['name']?></name>
<?=$tmpIndent?>    <wr_name><?=$item['wr_name']?></wr_name>
<?=$tmpIndent?>    <datetime><?=$item['datetime']?></datetime>
<?=$tmpIndent?>    <datetime2><?=date('Y-m-d\TH:i:s+09:00', strtotime($item['datetime']))?></datetime2>
<?=$tmpIndent?>    <is_secret><?=strstr($item['wr_option'], "secret")?"true":"false"?></is_secret>
<?=$tmpIndent?>    <content><?=$tmpContent?></content>
<?=$tmpIndent?>    <content1><?=$item['content1']?></content1>
<?=$tmpIndent?>    <can_reply><?=$item['is_reply']?"true":"false"?></can_reply>
<?=$tmpIndent?>    <can_edit><?=$item['is_edit']?"true":"false"?></can_edit>
<?=$tmpIndent?>    <can_delete><?=$item['is_del']?"true":"false"?></can_delete>
<?php
    if($is_del){
?>
<?=$tmpIndent?>    <delete_link><?=$item['del_link']?></delete_link>
<?php
    }
    if($is_ip_view){
?>
<?=$tmpIndent?>    <ip><?=$item['ip']?></ip>
<?php
    }

?>
<?=$tmpIndent?>    <show_idx><?=$i?></show_idx>
<?php
    if($depthNext > $depthCurrent){
        $stackPnt++;
?>
<?=$tmpIndent?>    <comments>
<?php
    }

    $depthBefore = $depthCurrent;
    $depthCurrent = $depthNext;
}

if(count($list) != 0){
    $tmpIndent = $indentDefault;
    for($j = 0; $j < $stackPnt - 1; $j++){
        $tmpIndent .= "        ";
    }
?>
<?=$tmpIndent?>        </comment>
<?php
}
for($i = 0; $i < $stackPnt; $i++){
    $tmpIndent = $indentDefault;
    for($j = 0; $j < $stackPnt - $i - 1; $j++){
        $tmpIndent .= "        ";
    }

?>
<?=$tmpIndent?>    </comments>
<?=$tmpIndent?></comment>        
<?php
}
?>
    </comments>
    <query_string><?=$tmpQueryString?></query_string>

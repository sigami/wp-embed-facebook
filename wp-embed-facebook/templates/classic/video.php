<?php
use SIGAMI\WP_Embed_FB\Plugin;
use SIGAMI\WP_Embed_FB\Embed_FB;
$use_ratio = (Plugin::get_option('video_ratio') == 'true');
?>
<div class="wef-classic aligncenter" style="max-width: <?php echo $width ?>px">
    <?php echo $use_ratio ? '<div class="wef-relative-container wef-video">' : '' ?>
    <?php
    /** @noinspection PhpUndefinedVariableInspection */
    $url = $fb_data['source'];
    $file_array = explode('/',parse_url($url, PHP_URL_PATH));
    $file = end($file_array);
    $type_array = explode('.',$file);
    $type = end($type_array);
    $clean_type = strtolower($type);

    if( Embed_FB::is_raw('video') && $clean_type == 'mp4' ) : ?>
        <?php $end = isset($fb_data['format']) ? end($fb_data['format']) : $fb_data;  ?>

        <video controls poster="<?php echo $end['picture'] ?>" >
            <source src="<?php echo $fb_data['source'] ?>" type="video/<?php echo $clean_type ?>">
        </video>

    <?php else : ?>

        <div class="fb-video" data-allowfullscreen="true"
             data-href="/<?php echo $fb_data['from']['id'] ?>/videos/<?php echo $fb_data['id'] ?>">
        </div>

    <?php endif; ?>
    <?php echo $use_ratio ? '</div>' : '' ?>
</div>
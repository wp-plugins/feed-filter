<?php
require_once 'controls.php';

$plugin = &$feed_filter;

if ($controls->is_action('save')) {
    $options = stripslashes_deep($_POST['options']);
    $ips = preg_split("/[\n\r\t]+/", $options['ip_bad'], null, PREG_SPLIT_NO_EMPTY);
    $options['ip_bad'] = empty($ips)?array():$ips;

    $ips = preg_split("/[\n\r\t]+/", $options['ip_good'], null, PREG_SPLIT_NO_EMPTY);
    $options['ip_good'] = empty($ips)?array():$ips;

    $plugin->update_options($options);
}

if ($controls->is_action('clear')) {
    file_put_contents(dirname(__FILE__) . '/referrers.txt', '');
}

if ($action == 'reset') {
    $plugin->reset_options();
}

$controls->options = $plugin->get_options();
$controls->options['ip_bad'] = implode("\r", $controls->options['ip_bad']);
$controls->options['ip_good'] = implode("\r", $controls->options['ip_good']);
?>
<style type="text/css">
    .form-table {
        background-color: #fff;
        border: 2px solid #eee;
    }
    .form-table th {
        text-align: right;
        font-weight: bold;
    }
    h3 {
        margin-bottom: 0;
        padding-bottom: 0;
        margin-top: 20px;
        font-size: 13px;
    }
    .form-table textarea {
        font-family: monospace;
        font-size: 12px;
    }
</style>
<div class="wrap">

    <h2><?php echo $plugin->name; ?></h2>

<div style="border: 1px solid #6f6; background-color: #efe; padding: 10px;">
    <table>
        <tr>
        <td valign="middle" align="center">
            <a href="http://www.satollo.net/donations" target="_blank"><img src="<?php echo WP_PLUGIN_URL . '/include-me/donate.gif'; ?>"/></a></td>
        <td valign="middle" align="left">
            A donation is like a diamond: it's forever. There is <a href="http://www.satollo.net/donations" target="_blank">something
            to read about donations</a>.
        </td>
    </tr>
    </table>
</div>
    
    <h3>Documentation</h3>
    <p>
        First, never publish the full post content on feeds. Go to WordPress
        settings/reading and change the feed from full text to summary!
    </p>

    <form method="post" action="">
        <?php $controls->init(); ?>

        <h3>...</h3>

        <table class="form-table">
            <tr>
                <th>Delay</th>
                <td>
                    <?php $controls->text('delay', 3); ?> days
                </td>
            </tr>
            <tr>
                <th>Title</th>
                <td>
                    <?php $controls->text('title'); ?>
                    <br />
                    {title} is the original title.
                    <br />
                    <?php $controls->textarea('title_php'); ?>
                    <br />
                    Here some PHP code to manipulate the title before the former transformation is applied. $title is the variable
                    containing the title.
                </td>
            </tr>
            <tr>
                <th>Excerpt</th>
                <td>
                    <?php $controls->textarea('excerpt'); ?>
                    <br />
                    {excerpt} is the original excerpt.
                    <br />
                    <?php $controls->textarea('excerpt_php'); ?>
                    <br />
                    Here some PHP code to manipulate the excerpt before the former transformation is applied. $excerpt is the variable
                    containing the excerpt text.
                </td>
            </tr>
        </table>
        <p>
            <?php $controls->button('save', 'Save'); ?>
            <?php $controls->button('reset', 'Reset', 'Reset all options?'); ?>
        </p>

        <h3>Shame on...</h3>
        <table class="form-table">
            <tr>
                <th>IPs</th>
                <td>
                    <?php $controls->textarea('ip_bad'); ?>
                    <br />
                    One per line
                </td>
            </tr>
            <tr>
                <th>Delay</th>
                <td>
                    <?php $controls->text('delay_bad', 3); ?> days
                </td>
            </tr>
            <tr>
                <th>Title</th>
                <td>
                    <?php $controls->text('title_bad'); ?>
                    <br />
                    {title} is the original title.
                    <br />
                    <?php $controls->textarea('title_php_bad'); ?>
                    <br />
                    Here some PHP code to manipulate the title before the former transformation is applied. $title is the variable
                    containing the title.
                </td>
            </tr>
            <tr>
                <th>Excerpt</th>
                <td>
                    <?php $controls->textarea('excerpt_bad'); ?>
                    <br />
                    {excerpt} is the original excerpt.
                    <br />
                    <?php $controls->textarea('excerpt_php_bad'); ?>
                    <br />
                    Here some PHP code to manipulate the excerpt before the former transformation is applied. $excerpt is the variable
                    containing the excerpt text.
                </td>
            </tr>
        </table>

        <p>
            <?php $controls->button('save', 'Save'); ?>
            <?php $controls->button('reset', 'Reset', 'Reset all options?'); ?>
        </p>

        <h3>Very good friends</h3>
        <table class="form-table">
            <tr>
                <th>IPs</th>
                <td>
                    <?php $controls->textarea('ip_good'); ?>
                    <br />
                    One per line
                </td>
            </tr>
            <tr>
                <th>Delay</th>
                <td>
                    <?php $controls->text('delay_good', 3); ?> days
                </td>
            </tr>
            <tr>
                <th>Title</th>
                <td>
                    <?php $controls->text('title_good'); ?>
                    <br />
                    {title} is the original title.
                    <br />
                    <?php $controls->textarea('title_php_good'); ?>
                    <br />
                    Here some PHP code to manipulate the title before the former transformation is applied. $title is the variable
                    containing the title.
                </td>
            </tr>
            <tr>
                <th>Excerpt</th>
                <td>
                    <?php $controls->textarea('excerpt_good'); ?>
                    <br />
                    {excerpt} is the original excerpt.
                    <br />
                    <?php $controls->textarea('excerpt_php_good'); ?>
                    <br />
                    Here some PHP code to manipulate the excerpt before the former transformation is applied. $excerpt is the variable
                    containing the excerpt text.
                </td>
            </tr>
        </table>

        <p>
            <?php $controls->button('save', 'Save'); ?>
            <?php $controls->button('reset', 'Reset', 'Reset all options?'); ?>
        </p>

        <h3>Examples and documentation</h3>
        <p>
            The title. Any feed item has a title which is the same title of your posts. The title is very important
            on indexing side, so when someone republish your content, even if partial, it has at least the full title.
            If you title is good for search engines, it will be good for anyone who use it.
        </p>
        <p>
            An idea is to add a prefix to your title:<br />
            <code>
                My Site Name - {title}
            </code>
        </p>

        <p>
            The feed excerpt is usually a simple text, but it can contain HTML tag and not all reader (or republishers) strip them.
            So why not add in the excerpt a link to your site? Or a set of links to the latest posts? It's link  building!
        </p>

        <p>
            To extract the latest posts and to create links to them just use:<br />
            <code>
                <?php echo htmlspecialchars("\$excerpt .= '<br />' . wp_get_archives('type=postbypost&limit=4&format=custom&before=&after=<br />&echo=0');"); ?>
            </code>
        </p>

        <p>
            So now we know we can add links to feed excerpt. It's interesting, because you can add even bad links, like links
            to malaware sites, virus sites and so one. Don't worry... browser stop surfers from enter such sites and Google is very
            smart in penalize sites that link bad sites.
        </p>


        <h3>Logs</h3>
        <p>
        <?php $nc->select('log', array(0=>'no', 1=>'yes')); ?> enable tracking of feed readers? (the file is "referrers.txt" and can grow quickly
        but helps in identify the reader IPs)<br />
            <?php $controls->button('clear', 'Clear'); ?>
        </p>

        <pre style="width: 100%; font-size: 10px; height: 500px; overflow: auto"><?php echo @file_get_contents(dirname(__FILE__) . '/referrers.txt'); ?></pre>
    </form>
</div>
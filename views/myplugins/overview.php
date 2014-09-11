
<table class="default">
    <thead>
        <tr>
            <th><?= _("Name") ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <? foreach ($plugins as $marketplugin) : ?>
        <tr>
            <td><?= htmlReady($marketplugin['name']) ?></td>
            <td>
                <a href="<?= PluginEngine::getLink($plugin, array(), "myplugins/edit/".$marketplugin->getId()) ?>" data-dialog><?= Assets::img("icons/20/blue/edit") ?></a>
            </td>
        </tr>
        <? endforeach ?>
    </tbody>
</table>

<?
$sidebar = Sidebar::Get();
$sidebar->setImage(Assets::image_path("sidebar/plugin-sidebar.png"));
$actions = new ActionsWidget();
$actions->addLink(_("Neues Plugin eintragen"), PluginEngine::getURL($plugin, array(), "myplugins/add"), null, array('data-dialog' => 1));
$sidebar->addWidget($actions);


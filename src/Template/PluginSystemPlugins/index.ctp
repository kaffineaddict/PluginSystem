<div class="pluginSystemPlugins index large-9 medium-8 columns content">
    <h3><?= __('Plugins') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= __('Name') ?></th>
                <th><?= __('Author') ?></th>
                <th><?= __('Version') ?></th>
                <th><?= __('Active') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pluginSystemPlugins as $plugin): ?>
            <tr>
                <td><?= h($plugin['info']['name']) ?></td>
                <td><?= h($plugin['info']['author']) ?></td>
                <td><?= h($plugin['info']['version']) ?></td>
                <td><?= h($plugin['info']['active']) ?></td>
                <td class="actions">
                	<?php if($plugin['info']['active']) {
                		echo $this->Form->postLink(__('Deactivate'), ['action' => 'deactivate', $plugin['system_name']], ['confirm' => __('Are you sure you want to deactivate {0}?', $plugin['system_name'])]);
                	} else {
                		echo $this->Form->postLink(__('Activate'), ['action' => 'activate', $plugin['system_name']], ['confirm' => __('Are you sure you want to activate {0}?', $plugin['system_name'])]);
                	}
                	?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
   	<?php $pluginInstance::hook('index.view'); ?>
</div>
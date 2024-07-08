<div class="users index">
	<div class="subactions">
		<?php echo $this->Html->link(__('Create User'), array('action' => 'add'), array('class' => 'btn btn-small btn-primary')); ?>
	</div>
	<h2><?php echo __('Users'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="table table-striped">
		<thead>
			<tr>
				<th><?php echo $this->Paginator->sort('role', 'User Type'); ?></th>
				<th><?php echo $this->Paginator->sort('name'); ?></th>
				<th><?php echo $this->Paginator->sort('email'); ?></th>
				<th><?php echo $this->Paginator->sort('phone'); ?></th>
				<th><?php echo $this->Paginator->sort('login', 'Last Login'); ?></th>
				<th class="actions"></th>
			</tr>
		</thead>
		<?php $user_types = unserialize(USER_TYPES); ?>
		<tbody>
		<?php foreach ($users as $user): ?>
			<tr>
				<td><?php echo $user_types[$user['User']['role']]; ?></td>
				<td><?php echo $user['User']['name']; ?></td>
				<td><?php echo $this->Html->link($user['User']['email'], 'mailto:'.$user['User']['email']); ?></td>
				<td><?php echo $user['User']['phone']; ?></td>
				<td><?php echo !is_null($user['User']['login']) 
					? $this->Time->format('F jS, Y', strtotime($user['User']['login']))
					: '<span class="muted">Never</span>'; ?></td>
				<td class="actions">
					<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id']), array('class' => '')); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	
	<?php echo $this->Element('pagination'); ?>
</div>
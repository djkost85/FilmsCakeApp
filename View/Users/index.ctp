<h1>
    Users
</h1>

<div class="users list">

    <table>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Role</th>
    </tr>

    <?php foreach ($users as $user): ?>
    <tr>
        <td><?php echo $user['User']['id']; ?></td>
        <td>
            <?php
                echo $this->Html->link($user['User']['username'],
                    array('controller' => 'users', 'action' => 'edit', $user['User']['id'])
                );
            ?>
        </td>
        <td><?php echo $user['User']['role']; ?></td>
    </tr>
    <?php endforeach; ?>
    <?php unset($user); ?>
</table>

</div>
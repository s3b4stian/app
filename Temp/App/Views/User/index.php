<main>
    <h1>Users</h1>
    <div id="changePasswordDialog"></div>
    <div id="deleteDialog"><p>would you like delete this user (_username_)?</p><p>...please confirm action</p></div>
    <table>
        <thead>
            <tr>
                <th>name</th>
                <th>description</th>
                <th>created</th>
                <th>active</th>
                <th>online now</th>
                <th>tools</th>
            </tr>
        </thead>
        <tfoot></tfoot>
        <tbody>
            <?php foreach ($data->users as $user) {
    ?>
                <tr>
                    <td><?php echo $user->name ?></td>
                    <td><?php echo $user->description ?></td>
                    <td><?php echo $user->created ?></td>
                    <td>
                        <span class="<?php echo ($user->active === 0) ? 'no' :'' ?>active" onclick="User.<?php echo ($user->active === 0) ? 'enable' :'disable' ?>(this, <?php echo $user->getId();
    ?>)"></span>
                    </td>
                    <td></td>
                    <td>
                        <button class="icon key-16" onclick="User.setPassword(this, <?php echo $user->getId();
    ?>, '<?php echo $user->name ?>')"></button>
                        <?php if ($user->name !== 'root') {
    ?>
                        <button class="icon pencil-16"></button>
                        <button class="icon trash-16" onclick="User.delete(this, <?php echo $user->getId();
    ?>, '<?php echo $user->name ?>')"></button>
                        <?php 
}
    ?>
                    </td>
                </tr>  
            <?php 
} ?>
        </tbody>
    </table>
</main>
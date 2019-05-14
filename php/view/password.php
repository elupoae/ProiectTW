<!DOCTYPE html>
<?php
    foreach ($password->get_all() as $password) {

?>
<div class="expand-info" id="expand-info">
    <h2 class="title-info"><?= $password->getNameWebsite() ?></h2>
    <div class="second-info">
        <div>
            <p>Web site: <a class="website-info" target="_blank" rel="noopener noreferrer"
                            href="<?= $password->getLinkWebsite() ?>"><?= $password->getLinkWebsite() ?></a></p>
        </div>
        <div class="input-info">
            <p>Last change: </p>
            <input class="last-info" type="text" value="<?= $password->getLastDataChange() ?>">
        </div>
        <div class="input-info">
            <p>Username: </p>
            <input class="username-info" type="text" value="<?= $password->getUsername() ?>">
        </div>
        <div class="input-info">
            <p>Password:</p>
            <input class="password-info" type="password" value="<?= $password->getPassword() ?>"><br><br>
        </div>
        <input class="password-checkbox" type="checkbox" onclick="myFunction()">Show Password
        <div class="button-zone">
            <button class="button-edit" style="display: block">Edit</button>
            <button class="button-save" style="display: none">Save</button>
            <button class="button-remove">Remove</button>
        </div>
    </div>
</div>
<?php } ?>
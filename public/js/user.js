var User = {
    
    modOldUserName: {},
    modOldUserDescription:{},
    
    modButtonCancel: {},
    modButtonSave: {},
    
    enable: function (button, user_id) {

        var ajax = new Ajax();
        var url_req = url + 'user/' + user_id + '/enable';
        ajax.get(url_req)
                .done(function (response, xhr) {
                    button.className = 'active';
                    button.setAttribute('onclick', 'User.disable(this, ' + user_id + ')');
                });
    },
    disable: function (button, user_id) {

        var ajax = new Ajax();
        var url_req = url + 'user/' + user_id + '/disable';
        ajax.get(url_req)
                .done(function (response, xhr) {
                    button.className = 'noactive';
                    button.setAttribute('onclick', 'User.enable(this, ' + user_id + ')');
                });
    },
    changePassword: function (button, user_id, user_name)
    {
        var passDialog = document.getElementById('changePasswordDialog');
        
        passDialog.dialog.open({
            name: 'password change for ' + user_name + '...',
            buttons: {
                exit: function ()
                {
                    passDialog.dialog.close();
                    document.getElementById('newpassword').value = '';
                    document.getElementById('confirmpassword').value = '';
                },
                change: function ()
                {
                    User._doChangePassword(passDialog, user_id);
                }
            }
        });
        
        User._cleanPasswordForm();
    },
    modify: function (button, user_id)
    {
        var tr = button.parentNode.parentNode;
        
        User._createModifyForm(tr, user_id);
        User._createButtonModifyForm(tr, user_id)
        
    },
    modifySave: function (){
        var tr = button.parentNode.parentNode;
    },
    
    _modifyClear: function(user_id){
        
        delete this.modButtonCancel['user'+user_id];
        delete this.modButtonSave['user'+user_id];
        
        delete this.modOldUserName['user'+user_id];
        delete this.modOldUserDescription['user'+user_id];
    },
    modifyExit: function (button, user_id){
        
        var tr = button.parentNode.parentNode;
        var td = button.parentNode;
        
        var buttonCancel = this.modButtonCancel['user'+user_id];
        var buttonSave = this.modButtonSave['user'+user_id];
        

        td.removeChild(buttonCancel);
        td.removeChild(buttonSave);
        
        tr.cells[0].innerHTML = this.modOldUserName['user'+user_id];
        tr.cells[1].innerHTML = this.modOldUserDescription['user'+user_id];
        
        
        this._modifyClear(user_id);
        
    },
    _createModifyForm: function(tr, user_id){
        
        this.modOldUserName['user'+user_id] = tr.cells[0].innerHTML;
        this.modOldUserDescription['user'+user_id] = tr.cells[1].innerHTML;
        
        var inputUserName = document.createElement('input');
        var inputUserDescription = document.createElement('input');
         
        inputUserName.id = 'newusername_'+user_id;
        inputUserDescription.id = 'newuserdescription_'+user_id;
        
        inputUserName.value = tr.cells[0].innerHTML;
        inputUserDescription.value = tr.cells[1].innerHTML;
            
        inputUserName.setAttribute('size', 14);
        inputUserDescription.setAttribute('size', 34);
        
        tr.cells[0].innerHTML = '';
        tr.cells[1].innerHTML = '';
        
        tr.cells[0].appendChild(inputUserName);
        tr.cells[1].appendChild(inputUserDescription);
        
        
    },
    _createButtonModifyForm: function(tr, user_id){
        
        var buttonCancel = document.createElement('button');
        var buttonSave = document.createElement('button');
        
        buttonCancel.id = 'userbuttonmodify_'+user_id;
        buttonCancel.classList.add('icon');
        buttonCancel.classList.add('cross-16');
        buttonCancel.style.marginLeft = '10px';
        
        buttonSave.id = 'userbuttonsave_'+user_id;
        buttonSave.classList.add('icon');
        buttonSave.classList.add('save-16');
        
        buttonCancel.setAttribute('onclick','User.modifyExit(this,' + user_id + ')');
        buttonSave.setAttribute('onclick','User.modifySave(this,' + user_id + ')');
        
        this.modButtonCancel['user'+user_id] = buttonCancel;
        this.modButtonSave['user'+user_id] = buttonSave;
        
        
        tr.cells[4].appendChild(buttonCancel);
        tr.cells[4].appendChild(buttonSave);
        
    },
    delete: function (button, user_id, user_name)
    {
        var deleteDialog = document.getElementById('deleteDialog');
        var message = new String(deleteDialog.innerHTML);
        deleteDialog.innerHTML = message.replace('_username_', user_name);
        deleteDialog.dialog.open({
            name: 'delete?',
            buttons: {
                exit: function ()
                {
                    deleteDialog.dialog.close();
                    deleteDialog.innerHTML = message;
                },
                confirm: function () {

                    User._doDelete(button, user_id);
                    deleteDialog.dialog.close();
                    deleteDialog.innerHTML = message;
                }
            }
        });
    },
    _cleanPasswordForm: function ()
    {
        var messages = document.querySelectorAll('.message');
        var dialogContent = document.querySelector('.content');
        [].slice.call(messages).forEach(function (el, i) {
            
                dialogContent.removeChild(el);
        });
    },
    _doDelete: function (button, user_id) {

        var ajax = new Ajax();
        var url_req = url + 'user/' + user_id + '/delete';
        ajax.get(url_req)
                .done(function (response, xhr) {

                    var tr = button.parentNode.parentNode;
                    var tbody = tr.parentNode;
                    tbody.removeChild(tr);
                });
    },
    _doChangePassword: function (passDialog, user_id) {

        var newPassword = document.getElementById('newpassword');
        var confirmPassword = document.getElementById('confirmpassword');
        var ajax = new Ajax();
        var url_req = url + 'user/' + user_id + '/changePassword';
        var data = {
            new_password: newPassword.value,
            confirm_password: confirmPassword.value
        };
        ajax.post(url_req, data)
                .done(function (response, xhr) {
                    
                    User._cleanPasswordForm();
                    
                    var dialogContent = document.querySelector('.content');

                    var div = document.createElement('div');
                    div.classList.add('message');

                    switch (response) {
                        case 2:
                            div.innerHTML = 'please choose a new password :|';
                            div.classList.add('alert');
                            dialogContent.appendChild(div);
                            break;
                        case 1:
                            div.innerHTML = 'new password and confirm password are different :(';
                            div.classList.add('error');
                            dialogContent.appendChild(div);
                            break;
                        case 0:
                            div.classList.add('success');
                            div.innerHTML = 'password succesfully changed :)';
                            dialogContent.appendChild(div);
                            passDialog.dialog.removeButton('dButton_change');
                            newPassword.value = '';
                            confirmPassword.value = '';
                            break;
                    }
                });
    }
};


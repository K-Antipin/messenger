function getCookie(name) {
    let matches = document.cookie.match(
        new RegExp(
            '(?:^|; )' +
            name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') +
            '=([^;]*)'
        )
    );
    return matches ? decodeURIComponent(matches[1]) : undefined;
};

let isEdit = false;
let userId = getCookie('id');
const users = {
    0: userId,
    1: null
};

document.addEventListener('click', () => {
    if (document.querySelector('.message_menu')) document.querySelector('.message_menu').remove();
});
document.addEventListener('contextmenu', () => {
    if (document.querySelector('.message_menu')) document.querySelector('.message_menu').remove();
});

let contextmenu = document.querySelectorAll('.contact_list div ul li');
contextmenu.forEach((el) => {
    el.addEventListener('contextmenu', (event) => {
        event.preventDefault();
        event.stopPropagation();
        if (document.querySelector('.message_menu')) document.querySelector('.message_menu').remove();
        let menu = event.target.getAttribute('data-user-id') ? { text: 'Добавить в группу' } : { text: 'Удалить группу' };
        createMessageUnder(event.pageX, event.pageY, event, menu);
    });
    el.addEventListener('click', (event) => {
        event.preventDefault();
        let messWindow = document.querySelector('.message_window');
        let div = document.createElement('div');
        let form = document.forms.message_form;
        if (messWindow.childElementCount > 0) messWindow.firstElementChild.remove();
        messWindow.append(div)
        contextmenu.forEach((el) => {
            if (el.classList.contains('active_user')) el.classList.remove('active_user');
        });
        event.currentTarget.classList.add('active_user');
        if (event.currentTarget.childNodes[3]) {
            event.currentTarget.childNodes[3].classList.remove('messages');
            event.currentTarget.childNodes[3].textContent = '';
        }
        if (document.querySelector('.active_user #groupsmess.groupmess')) document.querySelector('.active_user #groupsmess.groupmess').classList.remove('groupmess');
        form.toUserId.value = event.target.getAttribute('data-user-id');
        if (event.target.href.includes('group')) {
            document.forms.message_form.toUserId.name = 'toGroupId';
            users[1] = event.target.getAttribute('data-group-id');
            document.forms.message_form.toUserId.value = users[1];
            url = '/chat/group';
        } else {
            users[1] = event.target.getAttribute('data-user-id');
            url = '/chat';
        }

        document.querySelector('#button-addon2').disabled = false;
        let messages = sendData(users, url);
        messages
            .then(result => {
                result.reverse().forEach(res => {
                    if (res.from_id != userId) {
                        let p = document.createElement('p');
                        p.style.textAlign = 'left'
                        p.innerText = parseInt(res.display) ? res.nickname + ': ' + res.text : res.email + ': ' + res.text;
                        div.appendChild(p);
                        div.lastChild.scrollIntoView();
                    } else {
                        let p = document.createElement('p');
                        p.style.textAlign = 'right';
                        p.style.paddingRight = '5px';
                        if (url == '/chat/group') {
                            p.setAttribute('data-group-id', res.id);
                        } else {
                            p.setAttribute('data-mess-id', res.id);
                        }
                        p.addEventListener('contextmenu', (event) => {
                            event.preventDefault();
                            event.stopPropagation();
                            if (document.querySelector('.message_menu')) document.querySelector('.message_menu').remove();
                            let menu = [{ text: 'Удалить сообщение' }, { text: 'Редактировать сообщение' }];
                            createMessageUnder(event.pageX, event.pageY, event, menu);
                        });
                        p.innerHTML = '<span>' + res.text + '</span>' + '<span> :' + (parseInt(res.display) ? res.nickname : res.email) + '</span>';
                        div.appendChild(p);
                        div.lastChild.scrollIntoView();
                    }
                })
            })
    });
});

async function sendData(data, url) {
    {
        let formData = new FormData();
        if (data) {
            Object.entries(data).forEach((entry) => {
                const [key, value] = entry;
                formData.append(key, value);
            });
        }

        let response = await fetch(url, {
            method: 'POST',
            body: formData,
        });

        if (response.ok) {
            let result = await response.json();
            return result;
        }
        return false;
    }
}

function createMessageUnder(x, y, event, data = false) {
    let message = document.createElement('div');
    message.style.cssText = "position:absolute; border: 2px solid black; border-radius: 10px; display: flex; flex-direction: column;";
    message.classList.add('message_menu')
    message.style.left = x + "px";
    message.style.top = y + "px";
    message.style.zIndex = 2;
    message.style.backgroundColor = 'grey';

    if (data) {
        let formData = new FormData();
        if (data instanceof Array) {
            data.forEach((el) => {
                let div = document.createElement('div');
                div.innerText = el.text;
                div.classList.add('contextmenu');
                div.addEventListener('click', (eventDiv) => {
                    let objData = new Object;
                    if (event.target.parentNode.getAttribute('data-mess-id')) {
                        objData.id = event.target.parentNode.getAttribute('data-mess-id');
                        objData.table = 'messenges';
                    }
                    if (event.target.parentNode.getAttribute('data-group-id')) {
                        objData.id = event.target.parentNode.getAttribute('data-group-id');
                        objData.table = 'groupmess';
                    }
                    if (el.action === 'addgroup') {
                        objData.table = 'groups';
                        sendData(
                            el,
                            '/groups/add'
                        ).then(result => {
                            if (result) console.log(result)
                        });
                    }
                    if (eventDiv.target.textContent === 'Удалить сообщение') {
                        sendData(
                            objData,
                            '/chat/delete'
                        ).then(result => {
                            if (result) console.log('Сообщение удалено.')
                        });
                        event.target.parentNode.remove();
                    }
                    if (eventDiv.target.textContent === 'Редактировать сообщение') {
                        isEdit = true;
                        let editTextTarget = event.target;
                        let formEdit = document.forms.message_form;
                        let editText;
                        formEdit.messageInput.value = editTextTarget.textContent;
                        formEdit.setAttribute('data-action', 'edit');
                        formEdit.messageInput.focus();
                        formEdit.messageInput.addEventListener('change', () => {
                            textEdit = formEdit.messageInput.value;
                        });
                        formEdit.addEventListener('submit', (event) => {
                            event.preventDefault();
                            editTextTarget.textContent = textEdit;
                            objData.text = textEdit;
                            sendData(
                                objData,
                                '/chat/update'
                            ).then(result => {
                                if (result) console.log('Сообщение отредактировано.', result)
                            });
                        });
                    }
                });
                message.append(div);
            });
        } else {
            let div = document.createElement('div');
            div.innerText = data.text;
            div.classList.add('contextmenu');
            div.addEventListener('click', ((eventDiv) => {
                if (event.target.getAttribute('data-user-id')) {
                    formData.append('id', event.target.getAttribute('data-user-id'));
                }

                if (eventDiv.target.textContent === 'Добавить в группу') {
                    let objData = new Object;
                    objData.table = 'groups';

                    sendData(
                        objData,
                        '/home/addgroup'
                    ).then(result => {
                        if (result) {
                            let menu = new Array;
                            result.forEach(el => {
                                menu.push({
                                    text: 'Групповой чат № ' + el.group_id,
                                    user_id: event.target.getAttribute('data-user-id'),
                                    group_id: el.group_id,
                                    action: 'addgroup'
                                });
                            });
                            createMessageUnder(x, y, event, menu);
                        }
                    });
                }
            }));

            message.append(div);
        }
    }

    document.querySelector('body').appendChild(message);
}

if (document.querySelector('.about_home')) {
    document.querySelector('.about_home').addEventListener('click', () => {
        location.href = '/';
    });
}

if (document.forms.about_form) {
    document.forms.about_form.addEventListener('submit', async (event) => {
        event.preventDefault();
        let response = await fetch('/about/update', {
            method: 'POST',
            body: new FormData(document.forms.about_form)
        });

        let result = await response.json();
        if (result.error) {
            document.forms.about_form.childNodes[5].firstElementChild.style.color = 'red';
            document.forms.about_form.childNodes[5].firstElementChild.innerText = result.error;
            document.forms.about_form.childNodes[5].childNodes[3].value = '';
        }
    });
}

if (document.forms.about_file) {
    document.forms.about_file.addEventListener('change', async (event) => {
        let response = await fetch('/about/update', {
            method: 'POST',
            body: new FormData(document.forms.about_file)
        });

        let result = await response.json();

        if (result.warning) {
            if (document.querySelector('.about_warning')) document.querySelector('.about_warning').remove();
            let span = document.createElement('span');
            span.innerText = result.warning;
            span.style = 'margin-bottom: 5px; color: red;';
            span.classList.add('about_warning');
            document.forms.about_file.prepend(span);
            document.forms.about_file.reset();
        }

        if (result.avatar) {
            if (document.querySelector('.about_avatar img')) {
                window.location.reload(true);
            } else {
                let img = document.createElement('img');
                img.src = '/assets/img/temp/' + result.avatar;
                img.style = 'width: 150px; max-width: 100%; height: auto; display: block;'
                document.querySelector('.about_avatar').append(img);
                if (document.querySelector('.about_warning')) document.querySelector('.about_warning').remove();
                document.forms.about_file.reset();
            }
        }
    });
}

if (document.forms.form_search) {
    document.forms.form_search.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (document.querySelector('.users_search_active')) document.querySelector('.users_search_active').classList.remove('users_search_active');
        if (document.querySelector('.form_contacts_submit_active')) document.querySelector('.form_contacts_submit_active').classList.remove('form_contacts_submit_active');
        let response = await fetch('/home/search', {
            method: 'POST',
            body: new FormData(document.forms.form_search)
        });

        let result = await response.json();
        if (result.error) {
            document.querySelector('.users_search_error').classList.add('users_search_active');
        }

        if (result.id) {
            document.querySelector('.form_contacts_submit').classList.add('form_contacts_submit_active');
            document.querySelector('.form_contacts input[type=text]').value = result.id;
        }
    });
}

if (document.forms.form_contacts) {
    document.forms.form_contacts.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (document.querySelector('.form_contacts_submit_active')) document.querySelector('.form_contacts_submit_active').classList.remove('form_contacts_submit_active');
        let response = await fetch('/home/contact', {
            method: 'POST',
            body: new FormData(document.forms.form_contacts)
        });

        let result = await response.json();
    });
}

window.addEventListener('click', () => {
    if (document.querySelector('.message_menu')) document.querySelector('.message_menu').remove();
});

window.addEventListener('contextmenu', () => {
    if (document.querySelector('.message_menu')) document.querySelector('.message_menu').remove();
});
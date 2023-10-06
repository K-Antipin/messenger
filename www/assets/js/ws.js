if (document.readyState !== 'loading') {
    web_socket();
} else {
    document.addEventListener('DOMContentLoaded', web_socket());
}

function web_socket() {

    const userId = getCookie('id');
    let userNickname;
    let userNotification;

    const socket = new WebSocket('ws://127.0.0.1:8888?userId=' + userId);
    socket.addEventListener('error', (error) => {
        alert('Ошибка соединения ' + error.message);
    });

    socket.addEventListener('open', (event) => {
        console.log('Connected to server.');
    });

    socket.addEventListener('close', (event) => {
        alert('Соединение закрыто, обновите страницу.');
        if (document.querySelectorAll('div.status.active')) {
            let active = document.querySelectorAll('div.status.active');
            active.forEach(el => {
                el.classList.remove('active');
            });
        }
    });

    socket.addEventListener('message', (event) => {

        const datas = JSON.parse(event.data);

        if (datas.action == 'Authorized') {
            if (datas.users.length > 1) {
                for (let i = 0; i < datas.users.length; i++) {
                    if (datas.users[i].userId == userId) continue;
                    if (document.querySelector('#userId' + datas.users[i].userId + ' .status')) document.querySelector('#userId' + datas.users[i].userId + ' .status').classList.add('active');
                }
            }

        }

        if (datas.action == 'Connected') {
            if (datas.userId != userId) {
                let user = document.querySelector('#userId' + datas.userId + ' .status');
                user.classList.add('active');
            } else {
                userNickname = datas.userNickname;
                userNotification = datas.userNotification;
            }
        }

        if (datas.action == 'PrivateMessage') {
            if (document.querySelector('.active_user') && users[1] == datas.fromUserId) {
                let messWindow = document.querySelector('.message_window div');
                if (datas.fromUserId != userId) {
                    let p = document.createElement('p');
                    p.style.textAlign = 'left'
                    p.innerText = datas.userNickname + ': ' + datas.text;
                    messWindow.appendChild(p);
                    messWindow.lastChild.scrollIntoView();
                } else {
                    let p = document.createElement('p');
                    p.style.textAlign = 'right'
                    p.style.paddingRight = '5px'
                    p.innerHTML = '<span>' + res.text + '</span>' + '<span> :' + datas.userNickname + '</span>';
                    messWindow.appendChild(p);
                    messWindow.lastChild.scrollIntoView();
                }
            } else {
                let messNum = document.querySelector('#userId' + datas.fromUserId + ' #messages');
                messNum.classList.add('messages');
                if (messNum.textContent) {
                    messNum.textContent = (parseInt(messNum.textContent) + 1);
                } else {
                    messNum.textContent = 1;
                }
            }

            if (userNotification) {
                let audio = new Audio('/assets/audio/mess.mp3')
                let play = audio.play();
                if (play !== undefined) {
                    play.then(_ => {
                        // Automatic playback started!
                        // Show playing UI.
                    })
                        .catch(error => {
                            // Auto-play was prevented
                            // Show paused UI.
                        });
                }
            }
        }

        if (datas.action == 'GroupMessage') {
            if (document.querySelector('#groupId' + datas.groupId + '.active_user')) {
                let messWindow = document.querySelector('.message_window div');
                if (datas.fromUserId != userId) {
                    let p = document.createElement('p');
                    p.style.textAlign = 'left';
                    p.innerText = datas.userNickname + ': ' + datas.text;
                    messWindow.appendChild(p);
                    messWindow.lastChild.scrollIntoView();
                } else {
                    let p = document.createElement('p');
                    p.style.textAlign = 'right'
                    p.style.paddingRight = '5px'
                    p.innerHTML = '<span>' + res.text + '</span>' + '<span> :' + datas.userNickname + '</span>';
                    messWindow.appendChild(p);
                    messWindow.lastChild.scrollIntoView();
                }
            } else {
                let groupsmess = document.querySelector('#groupId' + datas.groupId + ' #groupsmess');
                groupsmess.classList.add('groupmess');

            }
            if (userNotification) {
                let audio = new Audio('/assets/audio/mess.mp3')
                let play = audio.play();
                if (play !== undefined) {
                    play.then(_ => {
                        // Automatic playback started!
                        // Show playing UI.
                    })
                        .catch(error => {
                            // Auto-play was prevented
                            // Show paused UI.
                        });
                }
            }
        }

        if (datas.action == 'Disconnected') {
            if (datas.userId != userId) {
                let user = document.querySelector('#userId' + datas.userId + ' .status');
                user.classList.remove('active');
            }
        }
    });

    if (document.forms.message_form) {
        let form = document.forms.message_form;

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            let messWindow = document.querySelector('.message_window div');
            let mess = form.messageInput.value;
            let toUserId = form.toUserId.value;

            let p = document.createElement('p');
            p.style.textAlign = 'right'
            p.style.paddingRight = '5px'
            p.innerHTML = '<span>' + mess + '</span>' + '<span> :' + userNickname + '</span>';
            if (!isEdit) {
                messWindow.appendChild(p);
            }
            isEdit = false;
            messWindow.lastChild.scrollIntoView();
            let obj = {
                fromUserId: userId,
                toUserId: toUserId,
                groupId: null,
                action: 'PrivateMessage',
                text: mess
            }
            
            if (typeof (form.toGroupId) !== 'undefined') {
                obj.groupId = form.toGroupId.value;
                obj.action = 'GroupMessage';
                obj.userNickname = userNickname;
            }

            if (!form.getAttribute('data-action')) {
                socket.send(JSON.stringify(obj));
            } else {
                form.removeAttribute('data-action');
            }

            form.reset();
        });

        form.messageInput.addEventListener('keypress', function (event) {
            if (event.code == 'Enter' && !event.ctrlKey) {
                document.querySelector('#button-addon2').focus();
                document.querySelector('#button-addon2').click();
            }
            if (event.code == 'Enter' && event.ctrlKey) this.value = this.value + '\n';
        });
    }
}


function getCookie(name) {
    let matches = document.cookie.match(
        new RegExp(
            '(?:^|; )' +
            name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') +
            '=([^;]*)'
        )
    );
    if (matches) return decodeURIComponent(matches[1]);
    return undefined;
}
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <form class="form-container" v-if="usertype === 'non'">
                        <div class="form-title">
                            <h2>Login</h2>
                        </div>
                        <div class="form-title"><label for="acc">Account</label> </div>
                        <input class="form-field" type="text" id="acc" v-model="loginData.acc" placeholder="請輸入帳號">
                        <br>
                        <div class="form-title"><label for="psd">Password</label></div>
                        <input class="form-field" type="password" id="psd" v-model="loginData.psd" placeholder="請輸入密碼">
                        <button class="submit-button" type="button" @click="login">Submit</button>
                    </form>
                    <div style="border-width:3px" v-if="usertype === 'agent'" @mousemove="move">
                        <h3> 代理{{loginAcc}}
                            <button @click="chatroom">聊天室</button>
                            <button v-if="agent" @click="manager">聊天室管理</button>
                        </h3>
                        <button onclick="{location.href='http://192.168.0.123/edit-user'}">編輯</button>
                        <button onclick="{location.href='http://192.168.0.123/search-user'}">查詢</button>
                        <button type="button" @click="logout">登出</button>
                    </div>
                    <div v-if="usertype === 'member'">
                        <h2>會員{{loginAcc}}</h2><br>
                        <button type="button" @click="logout">登出</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        data() {
            return {
                useracc: '',
                usertype: '',
                loginData: {
                    acc: '',
                    psd: ''
                },
                loginMsg: '',
                checkMsg: '',
                loginAcc: '',
                agent: false,
            }
        },
        methods: {
            async chatroom() {
                window.name = localStorage.token;
                window.location = "http://127.0.0.1:3000";
            },
            async manager() {
                window.name = localStorage.token;
                window.location = "http://127.0.0.1:3010";
            },
            async login() {
                // const accResult = await this.$validator.validate('Account');
                // if (!accResult) {
                //     return;
                // }
                let MD5 = require('crypto-js/md5');
                let psd = MD5(this.loginData.psd);
                let psdData = psd.words[0].toString() + psd.words[1].toString() + psd.words[2].toString() + psd.words[3].toString();
                let loginData = {
                    acc: this.loginData.acc,
                    psd: psdData
                }
                console.log(loginData);
                await axios.post('login', loginData)
                    .then(res => {
                        console.log(res.data);
                        if (res.data.ret === 0) {
                            alert('登入成功');
                            localStorage.token = res.data.token;
                            if (res.data.ut == 0) {
                                this.usertype = 'agent';
                            } else {
                                this.usertype = 'member';
                            }
                            this.loginAcc = res.data.acc;
                            if (res.data.acc == 'Agent1' || res.data.acc == 'Agent2') {
                                this.agent = true;
                            }
                            this.timer();
                        } else {
                            alert('登入失敗');
                        }
                    })
                    .catch(ex => {});
            },
            async checkToken() {
                await axios.post('token', {
                        token: localStorage.token
                    })
                    .then(res => {
                        console.log(res.data);
                        if (res.data.ret === 0) {
                            this.isLogin = 'login';
                        }
                    })
                    .catch(ex => {});
                this.isChange = 'unchanged';
            },
            async logout() {
                await axios.post('logout', {
                        token: localStorage.token
                    })
                    .then(res => {
                        console.log(res.data);
                        if (res.data.ret === 0) {
                            alert('登出成功');
                            localStorage.removeItem("token");
                            this.loginAcc = '';
                            this.usertype = 'non';
                            clearTimeout(this.timeCount);
                        } else {
                            alert('登出失敗');
                        }
                    })
                    .catch(ex => {});
                this.isChange = 'unchanged';
            },
            async checklogin() {
                await axios.post('checklogin', {
                        token: localStorage.token
                    })
                    .then(res => {
                        console.log(res.data);
                        this.loginAcc = res.data.loginAcc;
                        let $tmpUT = res.data.usertype;
                        if ($tmpUT === 0) {
                            this.usertype = 'agent';
                        } else if ($tmpUT === 1) {
                            this.usertype = 'member';
                        }
                        this.usertype = 'non';
                    })
                    .catch();
            },
            async move() {
                clearTimeout(this.timeCount);
                await this.timer();
            },
            async timer() {
                this.timeCount = setTimeout(function() {
                    clearTimeout(this.timeCount);
                    localStorage.removeItem("token");
                    alert("閒置時間過久，請重新登入");
                    this.loginAcc = '';
                    this.usertype = 'non';
                    location.reload();
                }, 600000);
            },
        },
        mounted() {
            this.checklogin();
        }
    }
</script>

<style>
    tr th,
    tr td {
        padding: 5px 20px;
    }
    form {
        /* set width in form, not fieldset (still takes up more room w/ fieldset width */
        font: 100% verdana, arial, sans-serif;
        margin: auto;
        padding: 0;
        min-width: 500px;
        max-width: 600px;
        width: 800px;
        position: absolute;
        height: 230px;
        top: 0;
        bottom: 30%;
        left: 0;
        right: 0;
    }
    .form-container {
        border: 3px solid #f2e3d2;
        background: #c9b7a2;
        background: -webkit-gradient(linear, left top, left bottom, from(#f2e3d2), to(#c9b7a2));
        background: -webkit-linear-gradient(top, #f2e3d2, #c9b7a2);
        background: -moz-linear-gradient(top, #f2e3d2, #c9b7a2);
        background: -ms-linear-gradient(top, #f2e3d2, #c9b7a2);
        background: -o-linear-gradient(top, #f2e3d2, #c9b7a2);
        background-image: -ms-linear-gradient(top, #f2e3d2 0%, #c9b7a2 100%);
        -webkit-border-radius: 7px;
        -moz-border-radius: 7px;
        border-radius: 7px;
        -webkit-box-shadow: rgba(000, 000, 000, 0.9) 0 1px 2px, inset rgba(255, 255, 255, 0.4) 0 0px 0;
        -moz-box-shadow: rgba(000, 000, 000, 0.9) 0 1px 2px, inset rgba(255, 255, 255, 0.4) 0 0px 0;
        box-shadow: rgba(000, 000, 000, 0.9) 0 1px 2px, inset rgba(255, 255, 255, 0.4) 0 0px 0;
        font-family: 'Helvetica Neue', Helvetica, sans-serif;
        text-decoration: none;
        vertical-align: middle;
        min-width: 310px;
        padding: 20px;
        width: 310px;
    }
    .form-field {
        border: 1px solid #c9b7a2;
        background: #e4d5c3;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        border-radius: 10px;
        color: #c9b7a2;
        -webkit-box-shadow: rgba(255, 255, 255, 0.4) 0 1px 0, inset rgba(000, 000, 000, 0.7) 0 0px 0px;
        -moz-box-shadow: rgba(255, 255, 255, 0.4) 0 1px 0, inset rgba(000, 000, 000, 0.7) 0 0px 0px;
        box-shadow: rgba(255, 255, 255, 0.4) 0 1px 0, inset rgba(000, 000, 000, 0.7) 0 0px 0px;
        padding: 8px;
        margin-bottom: 20px;
        width: 280px;
    }
    .form-field:focus {
        background: #fff;
        color: #725129;
    }
    .form-container h2 {
        text-shadow: #fdf2e4 0 1px 0;
        font-size: 18px;
        margin: 0 0 10px 0;
        font-weight: bold;
        text-align: center;
    }
    .form-title {
        margin-bottom: 10px;
        color: #725129;
        text-shadow: #fdf2e4 0 1px 0;
    }
    .submit-container {
        margin: 8px 0;
        text-align: right;
    }
    .submit-button {
        border: 1px solid #447314;
        background: #6aa436;
        background: -webkit-gradient(linear, left top, left bottom, from(#8dc059), to(#6aa436));
        background: -webkit-linear-gradient(top, #8dc059, #6aa436);
        background: -moz-linear-gradient(top, #8dc059, #6aa436);
        background: -ms-linear-gradient(top, #8dc059, #6aa436);
        background: -o-linear-gradient(top, #8dc059, #6aa436);
        background-image: -ms-linear-gradient(top, #8dc059 0%, #6aa436 100%);
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        -webkit-box-shadow: rgba(255, 255, 255, 0.4) 0 1px 0, inset rgba(255, 255, 255, 0.4) 0 1px 0;
        -moz-box-shadow: rgba(255, 255, 255, 0.4) 0 1px 0, inset rgba(255, 255, 255, 0.4) 0 1px 0;
        box-shadow: rgba(255, 255, 255, 0.4) 0 1px 0, inset rgba(255, 255, 255, 0.4) 0 1px 0;
        text-shadow: #addc7e 0 1px 0;
        color: #31540c;
        font-family: helvetica, serif;
        padding: 8.5px 18px;
        font-size: 14px;
        text-decoration: none;
        vertical-align: middle;
    }
    .submit-button:hover {
        border: 1px solid #447314;
        text-shadow: #31540c 0 1px 0;
        background: #6aa436;
        background: -webkit-gradient(linear, left top, left bottom, from(#8dc059), to(#6aa436));
        background: -webkit-linear-gradient(top, #8dc059, #6aa436);
        background: -moz-linear-gradient(top, #8dc059, #6aa436);
        background: -ms-linear-gradient(top, #8dc059, #6aa436);
        background: -o-linear-gradient(top, #8dc059, #6aa436);
        background-image: -ms-linear-gradient(top, #8dc059 0%, #6aa436 100%);
        color: #fff;
    }
    .submit-button:active {
        text-shadow: #31540c 0 1px 0;
        border: 1px solid #447314;
        background: #8dc059;
        background: -webkit-gradient(linear, left top, left bottom, from(#6aa436), to(#6aa436));
        background: -webkit-linear-gradient(top, #6aa436, #8dc059);
        background: -moz-linear-gradient(top, #6aa436, #8dc059);
        background: -ms-linear-gradient(top, #6aa436, #8dc059);
        background: -o-linear-gradient(top, #6aa436, #8dc059);
        background-image: -ms-linear-gradient(top, #6aa436 0%, #8dc059 100%);
        color: #fff;
    }
</style>



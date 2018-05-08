<template>
    <div class="hello">
        <div v-if="isEdit === 'no'">
            <h1>會員編輯</h1>
            <div class="UserEdit">
                <button type="button" @click="isEdit = 'insert'">新增</button>
                <button type="button" @click="isEdit = 'update'">修改</button>
                <button type="button" @click="isEdit = 'delete'">刪除</button>
                <button onclick="{location.href='http://192.168.0.123/'}">返回</button>
            </div>
        </div>
        <div v-if="isEdit === 'insert'">
            <h1>新增會員</h1>
            <div class="UserInsert">
                <label for="account">帳號：</label>
                <input v-validate="{ required: true, regex: /^[0-9A-z]{3,10}$/ }" data-vv-validate-on="blur" data-vv-delay="500" type="text" id="account" v-model="user.account" name="Account" placeholder="請輸入帳號(3 ~ 10字元)">
                <font color="red"><span v-show="errors.has('Account')"> 帳號格式有誤（請勿輸入空格、特殊符號）</span></font>
                <br>
                <label for="password">密碼：</label>
                <input v-validate="{ required: true, regex: /.{3,20}/ }" data-vv-validate-on="blur" data-vv-delay="500" name="Password" id="password" type="password" v-model="user.password" placeholder="請輸入密碼(6 ~ 20字元)">
                <font color="red"><span v-show="errors.has('Password')"> 密碼長度有誤（3 ~ 20字元）</span></font>
                <br>
                <label for="password_confirm">密碼確認：</label>
                <input v-validate="'required|confirmed:Password'" data-vv-validate-on="blur" type="password" id="password_confirm" name="Password_Confirm">
                <font color="red"><span v-show="errors.has('Password_Confirm')">密碼驗證錯誤！</span></font>
                <br>
                <label>身份：</label>
                <input type="radio" id="i-agent" name="usertype" value="0" v-model="user.usertype">
                <label for="i-agent">代理</label>
                <input type="radio" id="i-member" name="usertype" value="1" v-model="user.usertype">
                <label for="i-member">客戶</label>
                <br>
                <label for="name">姓名：</label>
                <input id="name" type="text" v-model="user.name">
                <br>
                <label for="email">信箱：</label>
                <input v-validate="'email'" data-vv-validate-on="blur" id="email" name="Email" type="text" v-model="user.email">
                <font color="red"><span v-show="errors.has('Email')">信箱格式有誤！</span></font>
                <br>
                <label for="phone">電話：</label>
                <input id="phone" type="text" v-model="user.phone">
                <br>
                <button type="button" @click="insert">新增</button>
                <button type="button" @click="isEdit = 'no'">返回</button>
            </div>
        </div>
        <div v-if="isEdit === 'update'">
            <h1>修改會員</h1>
            <div class="UserUpdate">
                <label for="account">帳號：</label>
                <input id="account" type="text" v-model="user.account">
                <br>
                <label>身份：</label>
                <input type="radio" id="u-agent" name="usertype" value="0" v-model="user.usertype">
                <label for="u-agent">代理</label>
                <input type="radio" id="u-member" name="usertype" value="1" v-model="user.usertype">
                <label for="u-member">客戶</label>
                <br>
                <label for="name">姓名：</label>
                <input id="name" type="text" v-model="user.name">
                <br>
                <label for="email">信箱：</label>
                <input v-validate="'email'" data-vv-validate-on="blur" id="email" name="Email" type="text" v-model="user.email">
                <font color="red"><span v-show="errors.has('Email')">信箱格式有誤！</span></font>
                <br>
                <label for="phone">電話：</label>
                <input id="phone" type="text" v-model="user.phone">
                <br>
                <button type="button" @click="update">修改</button>
                <button type="button" @click="isEdit = 'no'">返回</button>
            </div>
        </div>
        <div v-if="isEdit === 'delete'">
            <h1>刪除會員</h1>
            <div class="UserDelete">
                <label for="account">帳號：</label>
                <input id="account" type="text" v-model="user.account">
                <label for="password">密碼：</label>
                <input id="password" type="text" v-model="user.password">
                <button type="button" @click="dele">刪除</button>
                <button type="button" @click="isEdit = 'no'">返回</button>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    export default {
        name: 'HelloWorld',
        data() {
            return {
                isEdit: 'no',
                user: {
                    account: null,
                    password: null,
                    usertype: null,
                    name: null,
                    email: null,
                    phone: null
                }
            }
        },
        methods: {
            async insert() {
                let accResult = await this.$validator.validate('Account');
                let psdResult = await this.$validator.validate('Password');
                let psdConfirmResult = await this.$validator.validate('Password_Confirm');
                let emailResult = await this.$validator.validate('Email');
                if (!accResult || !psdResult || !psdConfirmResult || !emailResult) {
                    alert("資料內容或格式有誤！");
                    return;
                }
                let MD5 = require('crypto-js/md5');
                let psd = MD5(this.user.password);
                let psdData = psd.words[0].toString()+psd.words[1].toString()+psd.words[2].toString()+psd.words[3].toString();
                let UserData = {
                    account: this.user.account,
                    psd: psdData,
                    usertype: this.user.usertype,
                    name: this.user.name,
                    email: this.user.email,
                    phone: this.user.phone,
                    token: localStorage.token
                }
                console.log(UserData);
                await axios.post('insert', UserData)
                    .then(res => {
                        console.log(res.data);
                        alert("新增成功！");
                        this.isEdit = 'no';
                    })
                    .catch(ex => {});
            },
            async update() {
                let emailResult = await this.$validator.validate('Email');
                if (!emailResult) {
                    alert("資料內容或格式有誤！");
                    return;
                }
                let MD5 = require('crypto-js/md5');
                let psd = MD5(this.user.password);
                let UserData = {
                    account: this.user.account,
                    psd: psd,
                    usertype: this.user.usertype,
                    name: this.user.name,
                    email: this.user.email,
                    phone: this.user.phone,
                    token: localStorage.token
                }
                console.log(UserData);
                await axios.post('update', UserData)
                    .then(res => {
                        console.log(res.data);
                    })
                    .catch(ex => {});
            },
            async dele() {
                if (confirm("確定要刪除此客戶？")) {
                    let MD5 = require('crypto-js/md5');
                    let psd = MD5(this.user.password);
                    let UserData = {
                        account: this.user.account,
                        psd: psd
                    }
                    console.log(UserData);
                    await axios.post('delete', UserData)
                        .then(res => {
                            console.log(res.data.ret);
                            if (res.data.ret == 1) {
                                alert('密碼錯誤！');
                            } else {
                                alert('刪除成功！');
                                this.isEdit = 'no';
                            }
                        })
                        .catch(ex => {});
                }
            }
        },
        mounted() {}
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
    h1,
    h2 {
        font-weight: normal;
    }
    ul {
        list-style-type: none;
        padding: 0;
    }
    li {
        display: inline-block;
        margin: 0 10px;
    }
    a {
        color: #42b960;
    }
    .management {
        margin-bottom: 30px;
    }
    table {
        margin: 0 auto;
    }
    th,
    td {
        padding: 5px 30px;
    }
</style>

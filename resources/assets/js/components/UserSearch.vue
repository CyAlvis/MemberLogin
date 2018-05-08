<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div v-show="isEdit === 'no'">
                        <fieldset>
                            <legend>輸入查詢條件</legend>
                            <div>
                                <label for="Account">帳號：</label>
                                <input type="text" id="Account" v-model="check.account">
                                <button type="button" @click="search">搜尋使用者</button>
                                <button onclick="{location.href='http://192.168.0.123/'}">返回</button>
                            </div>
                            <div>
                                <input type="checkbox" id="agent" value="agent" v-model="check.agent">
                                <label for="agent">代理</label>
                                <input type="checkbox" id="member" value="member" v-model="check.member">
                                <label for="member">客戶</label>
                            </div>
                        </fieldset>
                        <br>
                        <div>
                            <table>
                                <tr>
                                    <th>帳號</th>
                                    <th>暱稱</th>
                                    <th>身份</th>
                                    <th>信箱</th>
                                    <th>電話</th>
                                </tr>
                                <tr>
                                    <tr v-for="(member) in memberList">
                                        <td>{{member.Account}}</td>
                                        <td>{{member.Name}}</td>
                                        <td>{{member.UserType == 0 ? '代理' : '客戶'}}</td>
                                        <td>{{member.Email}}</td>
                                        <td>{{member.Phone}}</td>
                                        <button type="button" @click="toupdate(member)">修改</button>
                                        <button type="button" @click="todele(member)">刪除</button>
                                    </tr>
                            </table>
                        </div>
                    </div>
                    <div v-if="isEdit === 'update'">
                        <h2>修改會員：{{editAcc}}</h2>
                        <div class="UserUpdate">
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
                        <h2>刪除會員：{{editAcc}}</h2>
                        <div class="UserDelete">
                            <label for="password">請輸入使用者密碼確認：</label>
                            <input id="password" type="password" v-model="user.password">
                            <button type="button" @click="dele">刪除</button>
                            <button type="button" @click="isEdit = 'no'">返回</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from "axios";
    export default {
        data() {
            return {
                isEdit: 'no',
                editAcc: '',
                memberList: [],
                resultID: '',
                resultUT: '',
                check: {
                    account: "",
                    agent: "",
                    member: "",
                    token: localStorage.token
                },
                user: {
                    password: '',
                    usertype: '',
                    name: '',
                    email: '',
                    phone: ''
                }
            };
        },
        methods: {
            async toupdate($mem) {
                this.isEdit = 'update';
                this.editAcc = $mem.Account;
                this.user.usertype = $mem.UserType;
                this.user.name = $mem.Name;
                this.user.email = $mem.Email;
                this.user.phone = $mem.Phone;
            },
            async todele($memacc) {
                this.isEdit = 'delete';
                this.editAcc = $memacc.Account;
            },
            async search() {
                await axios
                    .post("search", this.check)
                    .then(res => {
                        console.log(res.data);
                        if (res.data === 0) {
                            alert('請選擇代理或客戶！');
                        } else if (res.data !== 1) {
                            console.log(res.data);
                            this.memberList = res.data["out"];
                            this.resultID = res.data["id"];
                            this.resultUT = res.data["UT"];
                        } else {
                            alert('權限不足！');
                        }
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
                    account: this.editAcc,
                    psd: psd,
                    usertype: this.user.usertype,
                    name: this.user.name,
                    email: this.user.email,
                    phone: this.user.phone,
                    token: localStorage.token,
                    searchAcc: this.check.account,
                    resultID: this.resultID,
                    resultUT: this.resultUT
                }
                console.log(UserData);
                await axios.post('update', UserData)
                    .then(res => {
                        console.log(res.data);
                        this.memberList = res.data;
                        this.isEdit = 'no';
                    })
                    .catch(ex => {});
            },
            async dele() {
                if (confirm("確定要刪除此客戶？")) {
                    let MD5 = require('crypto-js/md5');
                    let psd = MD5(this.user.password);
                    let UserData = {
                        account: this.editAcc,
                        psd: psd,
                        resultID: this.resultID,
                        resultUT: this.resultUT
                    }
                    console.log(UserData);
                    await axios.post('delete', UserData)
                        .then(res => {
                            console.log(res.data);
                            if (res.data.ret == 1) {
                                alert('密碼錯誤！');
                            } else {
                                alert('刪除成功！');
                                this.memberList = res.data;
                                this.isEdit = 'no';
                            }
                        })
                        .catch(ex => {});
                }
            }
        },
        mounted() {}
    };
</script>

<style>
    tr th,
    tr td {
        padding: 5px 20px;
    }
</style>
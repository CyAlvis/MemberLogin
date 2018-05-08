<template>
    <div>
        <h3>資料:</h3>
        <h3>id:<input type="text" v-model="id">name:<input type="text" v-model="name"></h3>
        <h3>get</h3>
        <div>
            <button @click="get(1)">查詢</button>
            <button @click="get(2)">新增</button>
            <button @click="get(3)">修改</button>
            <button @click="get(4)">刪除</button>
        </div>
        <h3>post</h3>
        <div>
            <button @click="post(1)">查詢</button>
            <button @click="post(2)">新增</button>
            <button @click="post(3)">修改</button>
            <button @click="post(4)">刪除</button>
        </div>
        <h3>put</h3>
        <div>
            <button @click="put(1)">查詢</button>
            <button @click="put(2)">新增</button>
            <button @click="put(3)">修改</button>
            <button @click="put(4)">刪除</button>
        </div>
        <h3>delete</h3>
        <div>
            <button @click="dele(1)">查詢</button>
            <button @click="dele(2)">新增</button>
            <button @click="dele(3)">修改</button>
            <button @click="dele(4)">刪除</button>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        data() {
            return {
                id: null,
                name: null,
            }
        },
        methods: {
            async get(type) {
                await axios.get('get', {params: {'type': type, 'id': this.id, 'name': this.name}} )
                    .then(res => {
                        console.log(res.data);
                    })
                    .catch(ex => {});
            },
            async post(type) {
                await axios.post('post', {type: type,id: this.id, name: this.name})
                    .then(res => {
                        console.log(res.data);
                    })
                    .catch(ex => {});
            },
            async put(type) {
                await axios.put('put', {type: type, id: this.id, name: this.name})
                    .then(res => {
                        console.log(res.data);
                    })
                    .catch(ex => {});
            },
            async dele(type) {
                await axios.delete('delete/'+type+'/'+this.id+'/'+this.name)
                    .then(res => {
                        console.log(res.data);
                    })
                    .catch(ex => {});
            },
            mounted() {}
        }
    }
</script>
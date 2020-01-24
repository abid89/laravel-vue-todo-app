<template>
    <section class="todoapp">
        <header class="header">
            <h1>todos</h1>
        </header>
        <div class="todo_header">
            <input v-if="items.all !== 0" type="checkbox" class="toggle-all" :checked="items.checked == 1 ? true: false" ref="checkall">
            <label v-if="items.all !== 0" v-on:click.prevent="actionAll()"></label>
            <input type="text" v-model="todo.title" @keyup.enter="addTodo" autofocus="autofocus" placeholder="What needs to be done?" class="new-todo">
        </div>
        <ul class="todo-list">
            <li class="todo" :class="{ completed: todo.completed }" v-for="todo in todos" :todo="todo" :key="todo.id">
                <div class="view">
                    <input v-if="todo !== editingTask" type="checkbox" v-model="todo.completed" class="toggle" v-on:click.prevent="completeTodo(todo.id)">
                    <label v-if="todo !== editingTask" @dblclick="editTask(todo)">{{todo.title}}</label>
                    <input class="edit" v-if="todo === editingTask" v-autofocus @keyup.enter="endEditing(todo)" @blur="endEditing(todo)" type="text" v-model="todo.title">
                    <button v-if="todo !== editingTask" @click="removeTodo(todo.id)" class="destroy"></button>
                </div>
            </li>
        </ul>
        <div class="todo_footer" v-if="items.all !== 0">
            <div class="todo_left">{{ items.active }} item{{ (items.active > 1) ? 's' : '' }} left</div>
            <div class="todo_center">
                <button @click="all_menu()" class="my-link" :class="{isactive : activeTab == 'all'}">All</button>
                <button @click="active_menu()" class="my-link" :class="{isactive : activeTab == 'active'}">Active</button>
                <button @click="completed_menu()" class="my-link" :class="{isactive : activeTab == 'completed'}">Completed</button>
            </div>
            <div class="todo_right">
                <button v-if="items.complete > 0" @click="clear_completed()" class="clear-completed">Clear completed</button>
            </div>
        </div>
    </section>
</template>
<script>
    export default {
        data() {
            return {
                todo: {},
                todos: [],
                items: [],
                editingTask: {},
                activeTab:'all',
                checkvalue: 1
            }
        },
        created() {
            this.fetchTodoList(this.activeTab)
            this.getItem()
        },
        methods: {
            getItem(){
                this.axios
                    .get('/api/get_item')
                    .then(response => {
                        this.items = response.data;
                    });
            },
            fetchTodoList(tab) {
                this.axios
                    .get('/api/todos/'+tab)
                    .then(response => {
                        this.todos = response.data;
                    });
            },
            addTodo() {
                this.axios
                    .post('/api/todos', this.todo)
                    .then(response => (
                        //this.$router.push({name: 'home'})
                        //console.log(response.data)
                        this.todo = {},
                        this.fetchTodoList(this.activeTab),
                        this.getItem()
                    ))
                    .catch(error =>{
                        if(error.response.status==422){
                            alert("What needs to be done?")
                        }
                    })
                    .finally(() => this.loading = false)
            },
            editTask(toto) {
                this.editingTask = toto;
            },
            endEditing(todo) {
                this.editingTask = {};
                if (todo.title.trim() === '') {
                    this.removeTodo(todo.id);
                } else {
                    axios.post('/api/edit_todo', todo).then(result => {
                        //console.log('Updated')
                    }).catch(error => {
                        //console.log(error);
                    });
                }
            },
            removeTodo(id) {
                axios.post('/api/delete_todo/' + id).then(result => {
                    this.fetchTodoList(this.activeTab)
                    this.getItem()
                }).catch(error => {
                    //console.log(error);
                });
            },
            completeTodo(id){
                axios.post('/api/complete_todo/' + id).then(result => {
                    this.fetchTodoList(this.activeTab)
                    this.getItem()
                }).catch(error => {
                    //console.log(error);
                });
            },
            clear_completed(){
                axios.post('/api/clear_completed').then(result => {
                    this.fetchTodoList(this.activeTab)
                    this.getItem()
                }).catch(error => {
                    //console.log(error);
                });
            },
            all_menu(){
                this.activeTab='all'
                this.fetchTodoList(this.activeTab)
                this.getItem()
            },
            active_menu(){
                this.activeTab='active'
                this.fetchTodoList(this.activeTab)
                this.getItem()
            },
            completed_menu(){
                this.activeTab='completed'
                this.fetchTodoList(this.activeTab)
                this.getItem()
            },
            actionAll(){
                if(this.$refs.checkall.checked == true) {
                    this.checkvalue=0
                }else{
                    this.checkvalue=1;
                }
                axios.post('/api/action_all/'+this.checkvalue).then(result => {
                    this.fetchTodoList(this.activeTab)
                    this.getItem()
                }).catch(error => {
                    //console.log(error);
                });
            }
        }
    };
</script>
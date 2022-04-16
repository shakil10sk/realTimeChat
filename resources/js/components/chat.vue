<template>
    <div class="chat card">
        <div class="scrollable card-body" ref="hasScrolledToBottom">
            <template v-for="message in messages">
                <div class="message message-receive" v-if="user.id != message.user.id">
                    <p>
                        <strong class="primary-font">
                            {{message.user.name}}:
                        </strong>
                        {{message.message}}
                    </p>
                </div>
                <div class="message message-send" v-else>
                    <p>
                        <strong>
                            {{message.user.name}}:
                        </strong>
                        {{message.message}}
                    </p>
                </div>
            </template>
        </div>
        <div class="chat-form input-group">
            <input type="text" id="btn-input" name="message" class="form-control input-sm message-" placeholder="Type Your message here...." v-model="newMessage" @keyup.enter="addMessage">

            <span class="input-group-btn">
                <button class="btn btn-primary" id="btn-chat" @click="addMessage">
                    Send
                </button>
            </span>
        </div>
    </div>
</template>


<script>
    import { reactive,inject,ref,onMounted,onUpdate } from 'vue';
    import axios from 'axios';
    export default{
        props:['user'],
        setup(props){
            let messages = ref([])
            let newMessage = ref('');
            let hasScrolledToBottom = ref('');

            onMounted(()=>{
                fetchMessages();
            });

            onUpdate: ()=>{
                scrollBottom();
            };

            Echo.private('chat-channel')
            .listen('SendMesage',(e) => {
                messages.value.push({
                    message:e.message.message,
                    user: e.user
                });
            });

            const fetchMessages = async()=>{
                axios.get('/message').then(response => {
                    messages.value = response.data;
                });
            }

            const addMessage = async()=>{
                let user_message = {
                    user: props.user,
                    message : newMessage.value
                };
                messages.value.push(user_message);
                axios.post('/message',user_message).then(response => {
                    console.log(response.data);
                });
                newMessage.value = '';
            }

            const scrollBottom = () => {
                if(messages.value.length > 1){
                    let el = hasScrolledToBottom.value;
                    el.scrollTop = el.scrollHeight;
                }
            }

            return{ 
                messages,
                newMessage,
                addMessage,
                fetchMessages,
                hasScrolledToBottom
            }
        }
    }
</script>
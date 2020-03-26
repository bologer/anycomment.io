import axios from 'axios';

axios.defaults.baseURL = window.anyCommentApiSettings && window.anyCommentApiSettings.restUrl;

axios.defaults.withCredentials = true;
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
axios.defaults.headers.put['Content-Type'] = 'application/x-www-form-urlencoded';
axios.defaults.headers.common['Accept'] = 'application/json; charset=UTF-8';

export default axios;

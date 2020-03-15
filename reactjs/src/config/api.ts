import axios from 'axios';

axios.defaults.baseURL =
    process.env.ENV === 'dev'
        ? process.env.API_URL
        : // @ts-ignore
        'anyCommentApiSettings' in window
            ? window.anyCommentApiSettings.restUrl
            : null;

axios.defaults.withCredentials = true;
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
axios.defaults.headers.put['Content-Type'] = 'application/x-www-form-urlencoded';
axios.defaults.headers.common['Accept'] = 'application/json; charset=UTF-8';

export default axios;

import React from 'react'
import CommentList from './components/CommentList'
import CommentCopyright from './components/CommentCopyright'
import AnyCommentComponent from "./components/AnyCommentComponent"
import './css/app.css'
import {ToastContainer} from 'react-toastify'
import {toast} from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import GlobalHeader from "./components/GlobalHeader";
import CommonHelper from "./components/helpers/CommonHelper";


/**
 * App is main compontent of the application.
 */
class App extends AnyCommentComponent {
    constructor() {
        super();

        this.state = {
            shouldLoad: false,
            rootElement: 'anycomment-root',
            rootElementInner: 'anycomment-root-inner'
        };

        this.handleLoadOnScroll = this.handleLoadOnScroll.bind(this);
        this.handleScrollToComments = this.handleScrollToComments.bind(this);
        this.handleErrors = this.handleErrors.bind(this);
    }


    /**
     * Handle situation when required to load comments on scroll to them.
     */
    handleLoadOnScroll() {
        const {shouldLoad} = this.state;

        if (shouldLoad) {
            return false;
        }

        const self = this,
            {options} = this.props.settings;

        /**
         * When load on scroll is not enabled or
         * there is scroll to comments or specific comment in the url hash.
         */
        if (this.commentsVisible() || !options.isLoadOnScroll || (this.hasCommentSectionAnchor() || this.hasSpecificCommentAnchor())) {
            self.setState({shouldLoad: true});
        }

        if (!shouldLoad) {
            window.addEventListener('scroll', function () {
                if (self.commentsVisible()) {
                    window.removeEventListener('scroll', function () {
                    });
                    self.setState({shouldLoad: true});
                }
            });
        }
    }

    commentsVisible() {
        const root = document.getElementById('anycomment-root');

        if (!root) {
            return false;
        }

        const tillTop = root.offsetTop,
            body = document.body,
            html = document.documentElement;

        const height = Math.max(body.scrollHeight, body.offsetHeight,
            html.clientHeight, html.scrollHeight, html.offsetHeight);

        if (height <= window.innerHeight) {
            return true;
        }

        let wH = window.innerHeight,
            currentTop = CommonHelper.scrollTop();

        wH = wH * 0.9;

        if ((currentTop + wH) > tillTop) {
            return true;
        }

        return false;
    }

    /**
     * Handle scroll to comments.
     */
    handleScrollToComments() {
        const self = this;
        if (this.hasCommentSectionAnchor()) {
            const interval = setInterval(function () {
                let el = document.getElementById(self.state.rootElement);
                if (el) {
                    self.moveToElement(self.state.rootElement);
                    clearInterval(interval);
                }
            }, 100);
        }
    }

    /**
     * Make plugin IE compatible.
     */
    maybeAddIEMeta() {
        const metas = document.getElementsByTagName('meta');

        let found = false;
        for (let i = 0; i < metas.length; i++) {
            if (metas[i].getAttribute('http-equiv') === 'X-UA-Compatible') {
                found = true;
                break;
            }
        }

        if (!found) {
            var meta = document.createElement('meta');
            meta.httpEquiv = "X-UA-Compatible";
            meta.content = "IE=edge";
            document.getElementsByTagName('head')[0].appendChild(meta);
        }
    }

    /**
     * Handle possible backend errors.
     */
    handleErrors() {
        const settings = this.getSettings();

        if (settings.errors) {
            settings.errors.map((message) => toast.error(message));
        }
    }

    componentDidMount() {
        this.handleScrollToComments();
        this.handleLoadOnScroll();
        this.handleErrors();
        this.maybeAddIEMeta();
    }

    render() {
        const {shouldLoad} = this.state;

        if (!shouldLoad) {
            return (null);
        }

        return (
            <div id={this.state.rootElementInner} className="anycomment">
                <ToastContainer/>
                <GlobalHeader/>
                <CommentList/>
                <CommentCopyright/>
            </div>
        );
    }

}

export default App;

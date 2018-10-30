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
        if (this.state.shouldLoad) {
            return false;
        }

        const self = this,
            {options} = this.props.settings;

        /**
         * When load on scroll is not enabled or
         * there is scroll to comments or specific comment in the url hash.
         */
        if (!options.isLoadOnScroll || (this.hasCommentSectionAnchor() || this.hasSpecificCommentAnchor())) {
            self.setState({shouldLoad: true});
        }

        const root = document.getElementById('anycomment-root');

        if (!root) {
            return;
        }

        const tillTop = root.offsetTop;

        if (window.outerHeight <= window.innerHeight) {
            self.setState({shouldLoad: true});
            return false;
        }

        window.addEventListener('scroll', function () {

            let wH = window.innerHeight,
                currentTop = CommonHelper.scrollTop();

            wH = wH * 0.9;

            if ((currentTop + wH) > tillTop) {
                window.removeEventListener('scroll', function () {
                });
                self.setState({shouldLoad: true});
            }
        });
    }

    /**
     * Handle scroll to comments.
     */
    handleScrollToComments() {
        const self = this;
        if (this.hasCommentSectionAnchor()) {
            const interval = setInterval(function () {
                let el = document.getElementById(self.state.rootElement);
                console.log(el);
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

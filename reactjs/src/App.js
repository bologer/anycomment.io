import React from 'react'
import CommentList from './components/CommentList'
import CommentCopyright from './components/CommentCopyright'
import AnyCommentComponent from "./components/AnyCommentComponent"
import './css/app.css'
import {ToastContainer} from 'react-toastify'
import {toast} from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import PageRating from './components/PageRating'

/**
 * App is main compontent of the application.
 */
class App extends AnyCommentComponent {
    constructor() {
        super();

        this.state = {
            shouldLoad: false,
            rootElement: 'anycomment-root-inner'
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
            {options} = this.props.settings,
            $ = window.jQuery;

        /**
         * When load on scroll is not enabled or
         * there is scroll to comments or specific comment in the url hash.
         */
        if (!options.isLoadOnScroll || (this.hasCommentSectionAnchor() || this.hasSpecificCommentAnchor())) {
            $(document).ready(function () {
                self.setState({shouldLoad: true});
            });
        }

        const root = $('#anycomment-root'),
            tillTop = root.offset().top;

        if (window.outerHeight <= window.innerHeight) {
            self.setState({shouldLoad: true});
            return false;
        }

        $(window).on('scroll', function () {

            let wH = window.innerHeight,
                currentTop = $(this).scrollTop();

            wH = wH * 0.9;

            if ((currentTop + wH) > tillTop) {
                $(window).off('scroll');
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
            const rootEl = '#' + this.state.rootElement;
            const interval = setInterval(function () {
                let el = document.getElementById(rootEl.replace('#', ''));
                if (el) {
                    self.moveToElement(rootEl);
                    clearInterval(interval);
                }
            }, 100);
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
    }

    render() {
        const {shouldLoad} = this.state;

        if (!shouldLoad) {
            return (null);
        }

        return (
            <div id={this.state.rootElement} className="anycomment">
                <ToastContainer/>
                <PageRating/>
                <CommentList/>
                <CommentCopyright/>
            </div>
        );
    }

}

export default App;

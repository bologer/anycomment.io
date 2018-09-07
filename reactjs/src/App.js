import React from 'react';
import CommentList from './components/CommentList'
import CommentCopyright from './components/CommentCopyright'
import AnyCommentComponent from "./components/AnyCommentComponent";
import './css/comments.css'
import {ToastContainer} from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import $ from 'jquery'

/**
 * App is main compontent of the application.
 */
class App extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state = {
            shouldLoad: false,
            rootElement: 'anycomment-root-inner'
        };

        this.handleLoadOnScroll = this.handleLoadOnScroll.bind(this);
        this.handleScrollToComments = this.handleScrollToComments.bind(this);
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
            $(document).ready(function () {
                self.setState({shouldLoad: true});
            });
        }

        const root = $('#anycomment-root'),
            tillTop = root.offset().top;

        if ($(document).outerHeight() <= $(window).outerHeight()) {
            self.setState({shouldLoad: true});
            return false;
        }

        $(window).on('scroll', function () {

            let wH = $(window).height(),
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
                let el = $(rootEl);
                if (el.length) {
                    self.moveToElement(rootEl);
                    clearInterval(interval);
                }
            }, 100);
        }
    }

    componentDidMount() {
        this.handleLoadOnScroll();
        this.handleScrollToComments();
    }

    render() {
        const {shouldLoad} = this.state;

        if (!shouldLoad) {
            return (null);
        }

        return (
            <div id={this.state.rootElement} className={'anycomment anycomment-' + this.props.settings.options.theme}>
                <ToastContainer/>
                <CommentList/>
                <CommentCopyright/>
            </div>
        );
    }
}

export default App;

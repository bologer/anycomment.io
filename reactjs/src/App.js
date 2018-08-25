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
        };

        this.handleLoadOnScroll = this.handleLoadOnScroll.bind(this);
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

        if (!options.isLoadOnScroll) {
            $(document).ready(function () {
                self.setState({shouldLoad: true});
            });
        }

        const root = $('#anycomment-root'),
            tillTop = root.offset().top;

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

    componentDidMount() {
        this.handleLoadOnScroll();
    }

    render() {
        const {shouldLoad} = this.state;

        if (!shouldLoad) {
            return (null);
        }

        return (
            <div id="anycomment-root-inner" className={'anycomment anycomment-' + this.props.settings.options.theme}>
                <ToastContainer/>
                <CommentList/>
                <CommentCopyright/>
            </div>
        );
    }
}

export default App;

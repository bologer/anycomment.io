import React, {Component} from 'react'
import PageRating from './PageRating'
import ProfileDropdown from './ProfileDropdown'

export default class GlobalHeader extends Component {
    render() {
        return (
            <div className="anycomment anycomment-global-header">
                <PageRating/>
                <ProfileDropdown/>
            </div>
        );
    }
}
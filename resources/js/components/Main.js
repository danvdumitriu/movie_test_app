import React, { Component } from 'react';
import { BrowserRouter as Router, Route, Link} from 'react-router-dom';
import { browserHistory } from 'react-router';
import Header from './HeaderPage';
import './custom.css';
import Movies from "./Movies";

class Main extends Component {
    constructor(props) {
        super(props);
        this.state = {
        }
    }

    performSearch = (search) => {
        if(search) {
            location.hash = '/search/' + search;
        }
    }

    render() {

        return (
            <div>
                <Header onSearch={this.performSearch} />
                <Movies />
            </div>
        )
    }
}
export default Main;
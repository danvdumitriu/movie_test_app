import React, { Component } from 'react';
import Navbar from 'react-bulma-components/lib/components/navbar';
import SearchTop from "./SearchTop";
import Logo from './logo.svg';


class HeaderPage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            open: false
        }

        this.colors = {
            Default: '',
            primary: 'primary',
        };
    }

    performSearch = (search) => {
        this.props.onSearch(search);
    }

    render() {
        return (
            <Navbar
                color={this.colors.primary}
                fixed={'top'}
                active={false}
                transparent={false}
            >
                <Navbar.Brand>
                    <Navbar.Item renderAs="a" href="#/">
                        <Logo width={70} height={70}/>
                        <h2 className="primary_light is-hidden-mobile">Movie Test App</h2>
                    </Navbar.Item>
                    <Navbar.Item>
                        <SearchTop onSearch={this.performSearch} />
                    </Navbar.Item>
                </Navbar.Brand>
            </Navbar>
    )}

}
export default HeaderPage;
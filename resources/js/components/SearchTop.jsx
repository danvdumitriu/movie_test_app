import React, { Component } from 'react';
import { Control, Input, Checkbox } from 'react-bulma-components/lib/components/form';
import Button from 'react-bulma-components/lib/components/button';
import Section from 'react-bulma-components/lib/components/section';


class SearchTop extends Component {

    constructor(props) {
        super(props);
        this.state = {
            search:null
        }

        this.colors = {
            white: 'white',
        };
    }

    handleKeyPress = (e) => {
        if(e.key === 'Enter' && this.state.search){
            this.props.onSearch(this.state.search);
        }
    }

    handleInput = (key, e) => {
        this.setState({search: e.target.value});
    }

    handleButton = () => {
        if(this.state.search) {
            this.props.onSearch(this.state.search);
        }
    }

    render() {
        return (
            <Control>
                <input onKeyPress={this.handleKeyPress} className="input is-small search_text" type="text" placeholder="search" name="search" onChange={(e)=>this.handleInput('search', e)} />
                <Button className=""
                        color={this.colors.white}
                        onClick={this.handleButton}
                >
                    ok
                </Button>
            </Control>
        )
    }
}
export default SearchTop;
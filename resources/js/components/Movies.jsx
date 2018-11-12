import React, { Component } from 'react';
import Columns from 'react-bulma-components/lib/components/columns';
import Image from 'react-bulma-components/lib/components/image';
import Media from 'react-bulma-components/lib/components/media';
import Content from 'react-bulma-components/lib/components/content';
import { BrowserRouter as Router, Route, Link, Prompt, Redirect, Switch } from "react-router-dom";

const default_params = {
    keyword: null, //for search,
    search_results: [],
    no_search_results: null,
    listing: null
};

class Movies extends Component {

    constructor(props) {
        super(props);
        this.state = default_params;

    }

    componentDidMount = () => {
        this.process(null,null);
        window.addEventListener("hashchange", () => this.process(null,null), false);
    }
    componentWillUnmount = () => {
        window.removeEventListener("hashchange", () => this.process(null,null), false);
    }

    getWoeid = () => {
        return this.getHashParam("weather");
    }

    getKeyword = () => {
        return this.getHashParam("search");
    }

    getHashParam = (delimiter) => {
        if(!location || !location.hash) return null;
        let arr = location.hash.split("/"+delimiter+"/");
        if(arr[1]) return arr[1];
        return null;
    }

    process = (search) => {
        search = (search)?search:this.getKeyword();

        console.log(search);

        if(search) {
            this.setState({
                keyword: search

            }, () => {
                if (this.validateKeyWord()) {
                    this.fetchMovies();
                }
            });
        }
    }

    fetchMovies = (keyword) => {
        keyword = keyword?keyword:this.state.keyword;
        if(!keyword) {
            console.error("ERR#1: no search keyword!");
            return;
        }
        fetch('/api/movie/search/'+keyword)
            .then(response => {
                return response.json();
            })
            .then(data => {
                console.log("data",data,data.data.length);
                if(data.data.length<1) { //NO results for keyword
                    this.setState({no_search_results: true});

                } else { //there ARE results for keyword

                    this.setState({
                        search_results: data.data,
                        no_search_results: false,
                        listing: data.listing
                    }, () => {
                        console.log("state",this.state);
                    });
                }
            });
    }

    validateKeyWord = () => {
        if(this.state.keyword && this.state.keyword.length > 2) return true; //at least 3 characters
        return false;
    }

    renderRouter = () => {
        if(this.state.search_results) {
            if(this.state.listing) {
                return this.renderListing();
            } else {
                return this.renderDetails();
            }
        }
    }

    renderListing = () => {

        let results = this.state.search_results;

        console.log("results",results);

        return results.map((movie, i) => {
            let row_class = i%2?"odd_row":"even_row";
            return (
                <Columns className={"listing_row "+row_class} key={i}>
                    <Columns.Column size={1}>
                    </Columns.Column>
                    <Columns.Column size={1} className="listing_content">
                        <Router>
                            <Link to={"/#/movie/"+movie.id} onClick={() => this.process(null,location.woeid)}>
                                <img className="listing_image" src={movie.poster} />
                            </Link>
                        </Router>
                    </Columns.Column>
                    <Columns.Column size={1} className="listing_content">
                        <Router>
                            <Link to={"/#/movie/"+movie.id} onClick={() => this.process(null,location.woeid)}>
                                <h4 className="is-primary">{movie.title}</h4>
                            </Link>
                        </Router>
                    </Columns.Column>
                    <Columns.Column size={7} className="listing_content">
                        <div className="listing_description_text">
                            {movie.overview}
                        </div>
                    </Columns.Column>
                </Columns>
            );
        });

    }

    renderDetails = () => {
        return("");
    }

    render() {
        return (
            <div>
                {this.state.keyword && this.validateKeyWord() ? (<p>
                    Showing results for: <span className="primary"> {this.state.keyword} </span>
                </p>) : ("")}

                {this.state.keyword && !this.validateKeyWord() ? (<p>
                    Keyword has to be at least 3 characters long
                </p>) : ("")}

                {this.state.no_search_results && this.validateKeyWord()? (
                    <p>No results were found. Try changing the keyword!</p>
                ) : ("")}

                <div>
                    {this.renderRouter()}
                </div>

            </div>
        )}

}
export default Movies;
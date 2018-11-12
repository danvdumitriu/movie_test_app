import React, { Component } from 'react';
import Columns from 'react-bulma-components/lib/components/columns';
import Image from 'react-bulma-components/lib/components/image';
import Media from 'react-bulma-components/lib/components/media';
import Content from 'react-bulma-components/lib/components/content';
import { BrowserRouter as Router, Route, Link, Prompt, Redirect, Switch } from "react-router-dom";

const default_params = {
    keyword: null, //for search,
    search_results: null,
    no_search_results: null,
    listing: null,
    movie_id: null,
    movie_details: null
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

    getMovieId = () => {
        return this.getHashParam("movie");
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

    process = (search, movie_id) => {
        console.log("params11", search, movie_id);

        this.setState(default_params, () => {

            search = (search) ? search : this.getKeyword();
            movie_id = (movie_id) ? movie_id : this.getMovieId();

            console.log("params", search, movie_id);

            if (search) {
                this.setState({
                    keyword: search,
                    movie_id: null

                }, () => {
                    if (this.validateKeyWord()) {
                        this.fetchMovies();
                    }
                });
            } else if (movie_id) {
                this.setState({
                    keyword: null,
                    movie_id: movie_id

                }, () => {
                    this.fetchMovieDetails();
                });
            }
        });
    }

    fetchMovieDetails = (movie_id) => {
        movie_id = movie_id?movie_id:this.state.movie_id;
        if(!movie_id) {
            console.error("ERR#2: no movie id!");
            return;
        }

        fetch('/api/movie/id/'+movie_id)
            .then(response => {
                return response.json();
            })
            .then(data => {
                console.log("data",data,data.data.length);
                if(data.data.length<1) { //NO results
                    //this.setState({no_search_results: true});

                } else { //there ARE results

                    this.setState({
                        movie_details: data.data,
                        listing: data.listing
                    }, () => {
                        console.log("state",this.state);
                    });
                }
            });
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
                    this.setState({
                        no_search_results: true,
                        search_results: []
                    });

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
        if(this.state.search_results && this.state.listing) {
            return this.renderListing();
        } else if(this.state.movie_details && !this.state.listing) {
            return this.renderDetails();
        } else if(this.state.search_results && !this.state.listing) {
            return this.renderDetails();
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
                            <Link to={"/#/movie/"+movie.id} onClick={() => this.process(null,movie.id)}>
                                <img className="listing_image" src={movie.poster} />
                            </Link>
                        </Router>
                    </Columns.Column>
                    <Columns.Column size={1} className="listing_content">
                        <Router>
                            <Link to={"/#/movie/"+movie.id} onClick={() => this.process(null,movie.id)}>
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
        console.log("22details");
        let movie = this.state.movie_details?this.state.movie_details[0]:this.state.search_results[0];

        return (
            <Columns className="details_container">
                <Columns.Column size={1}>
                </Columns.Column>
                <Columns.Column size={2} className="details_content">
                    <img className="details_image" src={movie.poster} />
                </Columns.Column>
                <Columns.Column size={7} className="details_content">
                    <h2 className="primary">{movie.title}</h2>
                    <div className="listing_description_text">
                        {movie.overview}
                    </div>
                    <div className="details_cast">
                        {movie.actors.map((actor,i) => {
                            return this.renderActors(actor,i);
                        })}
                    </div>
                </Columns.Column>
                <Columns.Column size={1}>
                </Columns.Column>
            </Columns>
        );
    }

    renderActors = (actor, i) => {
        return (
            <Columns className="details_container" key={i}>
                <Columns.Column size={1}>
                </Columns.Column>
                <Columns.Column size={1} className="details_content">
                    <img className="details_actor_photo" src={actor.photo} />
                </Columns.Column>
                <Columns.Column size={3} className="details_content">
                    <h4 className="primary">{actor.name}</h4>
                </Columns.Column>
                <Columns.Column size={1}>
                    <p>...</p>
                </Columns.Column>
                <Columns.Column size={3} className="details_content">
                    <h4 className="primary">{actor.character}</h4>
                </Columns.Column>
            </Columns>
        );
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
import React from 'react';
import ReactDOM from 'react-dom';
import Main from "./components/Main";

if (document.getElementById('root')) {
    ReactDOM.render(
        <div className="App">
            <Main />
        </div>,
        document.getElementById('root'));
}

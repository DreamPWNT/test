import React, { useReducer, useState } from 'react';
import axios from 'axios';

import './App.css';

const API_PATH = '/test/test.php';

const initialState = {
    uuid: null,
    firstName: '',
    lastName: '',
    email: '',
    subject: '',
    isSaved: false,
    savingError: null
};

const reducer = (state, action) => {
    switch(action.type)
    {
        case 'SET_DATA':
            return {...state, ...action.payload}
        case 'SET_INPUT_VALUE':
            return {...state, [action.payload.name]: action.payload.value};
        case 'THROW_ERROR':
            return {...state, error: action.payload}
    }

    return {...state, [action.type]: action.payload}
}

const App = () => {
    const [state, dispatch] = useReducer(reducer, initialState);

    const inputHandle = e => {
        dispatch({
            type: 'SET_INPUT_VALUE',
            payload: {
                name: e.target.name,
                value: e.target.value
            }
        });
    }

    const handleFormSubmit = e => {
        e.preventDefault();
        axios({
            method: 'post',
            url: `${API_PATH}`,
            headers: { 'content-type': 'application/json' },
            data: state
        })
            .then(result => {
                dispatch({
                    type: 'SET_DATA',
                    payload: result
                });
            })
            .catch(error => {
                dispatch({
                    type: 'THROW_ERROR',
                    payload: error.message
                });
            });
    }

    return (
      <div className="App">
        <p>Contact Me</p>
        <div>
          <form action="/action_page.php">
            <label>First Name</label>
            <input type="text" id="fname" name="firstName" placeholder="Your name.."
                   onChange={inputHandle}
            />
            <label>Last Name</label>
            <input type="text" id="lname" name="lastName" placeholder="Your last name.."
                   onChange={inputHandle}
            />
            <label>Email</label>
            <input type="email" id="email" name="email" placeholder="Your email"
                   onChange={inputHandle}
            />
            <label>Subject</label>
            <textarea id="subject" name="subject" placeholder="Write something.."
                      onChange={inputHandle}
            ></textarea>
            <input type="submit" value="Submit" onClick={handleFormSubmit} />
          </form>
        </div>
      </div>
  );
}

export default App;

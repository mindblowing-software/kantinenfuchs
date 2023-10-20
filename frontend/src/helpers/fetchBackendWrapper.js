import { useAuthStore } from 'stores/RailsAuth';

export const fetchWrapper = {
    get: request('GET'),
    post: request('POST'),
    put: request('PUT'),
    delete: request('DELETE')
};

function request(method) {
    return (url, body, variables) => {
        const requestOptions = {
            method,
            headers: authHeader(url),
            credentials: "include"
        };
        if (body) {
            requestOptions.headers['Content-Type'] = 'application/json';
            requestOptions.body = JSON.stringify(body);
            if (variables) {
                requestOptions.variables = variables
            }
        }
        return fetch(url, requestOptions).then(handleResponse);
    }
}

// helper functionn
function authHeader(url) {
    // return auth header with jwt if user is logged in and request is to the backend rails api url

    const { token } = useAuthStore();

    console.log(token)

    const isLoggedIn = (token !== null);
    const isApiUrl = url.startsWith(process.env.BACKEND_RAILS_API);

    if (isLoggedIn && isApiUrl) {
        return { 
            Authorization: `${token}` 
        };
    } else {
        return {};
    }
}


async function handleResponse(response) {

    // check for error response
    if (!response.ok) {

        const { user, forceLogin } = useAuthStore();
        if ([401, 403].includes(response.status) && user) {
            // auto logout if 401 Unauthorized or 403 Forbidden response returned from api
            forceLogin();
        }

        // get error message from body or default to response status
        const error = (data && data.message) || response.status;
        return Promise.reject(error);
    }

    return response;
}
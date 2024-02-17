Rails.application.routes.draw do

  devise_for :users, controllers: {
    sessions: 'users/sessions',
    invitations: 'users/invitations',
  }

  resources :orders
  resources :customers

  # do not mess with devise ;-)
  resources :users, :only =>[:index, :show]
  
  # Define your application routes per the DSL in https://guides.rubyonrails.org/routing.html

  # Reveal health status on /up that returns 200 if the app boots with no exceptions, otherwise 500.
  # Can be used by load balancers and uptime monitors to verify that the app is live.
  get "up" => "rails/health#show", as: :rails_health_check

  get "dashboard", to: "dashboard#show"
  # Defines the root path route ("/")
  # root "posts#index"
  get "home/index"  
  root to: "home#index"
end

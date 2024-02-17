class UsersController < ApplicationController

  def index
    authorize!
    @users = User.all.order(:role, :customer_id, :lastname, :firstname)
  end

  def show
    @user = User.find_by_id(params[:id])
    authorize! @user
  end
end

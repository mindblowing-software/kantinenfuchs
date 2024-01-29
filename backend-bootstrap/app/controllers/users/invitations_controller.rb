class Users::InvitationsController < Devise::InvitationsController
  before_action :configure_permitted_parameters

  def after_accept_path_for(user)
    if user.admin?
      dashboard_path
    else
      new_order_path
    end
  end

  def new
    self.resource = resource_class.new
    resource.customer_id = params["customer_id"]
    render :new
  end


  protected

  # Permit the new params here.
  def configure_permitted_parameters
    devise_parameter_sanitizer.permit(:invite, keys: [:firstname, :lastname, :role, :customer_id])
  end
end
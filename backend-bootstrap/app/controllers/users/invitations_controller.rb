class Users::InvitationsController < Devise::InvitationsController
  before_action :configure_permitted_parameters

#  def update
#    if @current_user.role == 1
#      redirect_to root_path
#    else
#      super
#    end
#  end

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
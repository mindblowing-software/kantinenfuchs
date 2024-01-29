class OrderPolicy < ApplicationPolicy
  pre_check :allow_admins, :allow_owner
  # See https://actionpolicy.evilmartians.io/#/writing_policies
  #
  #def index?
  #  true
  #end

  #def update?
    # here we can access our context and record
  #  current_user.admin? || (current_user.customer.id == record.customer_id)
  #end

  # Scoping
  # See https://actionpolicy.evilmartians.io/#/scoping
  #
  # relation_scope do |relation|
  #   next relation if user.admin?
  #   relation.where(user: user)
  # end
  def allow_admins
    allow! if user.admin?
  end

  def allow_owner
    allow! if (user.customer.id == record.customer_id)
  end
end

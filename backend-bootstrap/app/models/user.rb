class User < ApplicationRecord
  # Include default devise modules. Others available are:
  # :confirmable, :lockable, :timeoutable, :trackable and :omniauthable
  devise :database_authenticatable, :registerable,
         :recoverable, :rememberable, :validatable, :invitable

  belongs_to :customer

  enum role: {Admin: 0, User: 1}

  def admin?
    role == "Admin"
  end
end

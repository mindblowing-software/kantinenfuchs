class Customer < ApplicationRecord
  has_many :users

  validates :name,  :presence => true
  validates :number_orders, :presence => true
end

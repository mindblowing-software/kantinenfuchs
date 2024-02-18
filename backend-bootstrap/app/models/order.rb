class Order < ApplicationRecord
  paginates_per 10
  belongs_to :customer
  belongs_to :user
end
